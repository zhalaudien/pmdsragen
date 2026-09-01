<?php

namespace App\Services;

use Config\MtaApi as MtaApiConfig;
use Config\Services;
use CodeIgniter\HTTP\CURLRequest;

class MtaApiService
{
    protected MtaApiConfig $config;
    protected CURLRequest $client;
    protected ?array $lastRateLimit = null;

    public function __construct(?MtaApiConfig $config = null)
    {
        $this->config = $config ?? config('MtaApi');
        $this->client = Services::curlrequest([
            'baseURI'     => rtrim($this->config->baseUrl, '/') . '/',
            'timeout'     => $this->config->timeout,
            'http_errors' => false, // Jangan lempar exception pada status code 4xx/5xx agar response JSON dapat ditangkap
        ]);
    }

    /**
     * Dapatkan status aktif konfigurasi
     */
    public function isEnabled(): bool
    {
        return $this->config->enabled && !empty($this->config->apiToken);
    }

    /**
     * Dapatkan informasi rate limit dari request terakhir
     */
    public function getLastRateLimit(): ?array
    {
        return $this->lastRateLimit;
    }

    /**
     * Kirim HTTP Request ke API MTA
     */
    protected function request(string $method, string $endpoint, array $options = []): array
    {
        if (!$this->config->enabled) {
            return [
                'success' => false,
                'message' => 'Integrasi API MTA sedang dinonaktifkan di konfigurasi.',
                'code'    => 503,
            ];
        }

        $url = str_starts_with($endpoint, 'http')
            ? $endpoint
            : (rtrim($this->config->baseUrl, '/') . '/' . ltrim($endpoint, '/'));

        // Setup Headers default
        $headers = [
            'Accept'      => 'application/json',
            'X-API-Token' => $this->config->apiToken,
            'User-Agent'  => 'Pemuda-MTA-Sragen/1.0',
        ];

        if (isset($options['headers'])) {
            $headers = array_merge($headers, $options['headers']);
            unset($options['headers']);
        }

        $options['headers'] = $headers;

        try {
            $response = $this->client->request(strtoupper($method), $url, $options);
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            // Tangkap header Rate Limiting
            if ($response->hasHeader('X-RateLimit-Limit') || $response->hasHeader('X-RateLimit-Remaining')) {
                $this->lastRateLimit = [
                    'limit'     => $response->getHeaderLine('X-RateLimit-Limit'),
                    'remaining' => $response->getHeaderLine('X-RateLimit-Remaining'),
                ];
            }

            $jsonData = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'success'    => false,
                    'message'    => 'Format response dari server MTA bukan JSON yang valid (HTTP ' . $statusCode . ').',
                    'raw'        => substr($body, 0, 500),
                    'statusCode' => $statusCode,
                ];
            }

            // Tambahkan statusCode jika belum ada di payload
            $jsonData['statusCode'] = $statusCode;

            if ($statusCode === 401) {
                $jsonData['message'] = $jsonData['message'] ?? 'Token API MTA tidak valid atau belum diatur.';
                $jsonData['success'] = false;
            } elseif ($statusCode === 403) {
                $jsonData['message'] = $jsonData['message'] ?? 'Akses ditolak: IP tidak diizinkan atau izin akses belum diberikan.';
                $jsonData['success'] = false;
            } elseif ($statusCode === 429) {
                $jsonData['message'] = $jsonData['message'] ?? 'Batas kuota request tercapai (Rate limit 60 req/menit).';
                $jsonData['success'] = false;
            }

            return $jsonData;

        } catch (\Throwable $e) {
            log_message('error', '[MtaApiService] Request Error: ' . $e->getMessage());
            return [
                'success'    => false,
                'message'    => 'Koneksi ke API MTA gagal: ' . $e->getMessage(),
                'statusCode' => 500,
            ];
        }
    }

    /**
     * Test Koneksi ke Server API MTA
     */
    public function testConnection(): array
    {
        $res = $this->getStatistik();
        return [
            'connected'     => ($res['success'] ?? false) === true,
            'statusCode'    => $res['statusCode'] ?? 500,
            'message'       => $res['message'] ?? (($res['success'] ?? false) ? 'Koneksi API MTA Terhubung Normal' : 'Gagal terhubung'),
            'rateLimit'     => $this->lastRateLimit,
            'data'          => $res['data'] ?? null,
            'baseUrl'       => $this->config->baseUrl,
            'tokenMasked'   => substr($this->config->apiToken, 0, 6) . '...' . substr($this->config->apiToken, -4),
        ];
    }

    /**
     * 1. GET /statistik — Statistik Umum Keseluruhan Data Warga
     */
    public function getStatistik(): array
    {
        return $this->request('GET', 'statistik');
    }

    /**
     * 2. GET /perwakilan — Daftar Perwakilan
     */
    public function getPerwakilanList(?string $search = null, int $limit = 50): array
    {
        $query = [];
        if (!empty($search)) {
            $query['search'] = $search;
        }
        if ($limit > 0) {
            $query['limit'] = min($limit, 200);
        }

        return $this->request('GET', 'perwakilan', ['query' => $query]);
    }

    /**
     * 3. GET /perwakilan/{uuid} — Detail Perwakilan & Daftar Cabang di dalamnya
     */
    public function getPerwakilanDetail(string $uuid): array
    {
        return $this->request('GET', 'perwakilan/' . urlencode($uuid));
    }

    /**
     * 4. GET /cabang — Daftar Cabang
     */
    public function getCabangList(?string $perwakilanUuid = null, ?string $search = null, int $limit = 100): array
    {
        $query = [];
        if (!empty($perwakilanUuid)) {
            $query['perwakilan'] = $perwakilanUuid;
        }
        if (!empty($search)) {
            $query['search'] = $search;
        }
        if ($limit > 0) {
            $query['limit'] = min($limit, 200);
        }

        return $this->request('GET', 'cabang', ['query' => $query]);
    }

    /**
     * 5. GET /cabang/{uuid} — Detail Cabang & Statistik Warga Cabang
     */
    public function getCabangDetail(string $uuid): array
    {
        return $this->request('GET', 'cabang/' . urlencode($uuid));
    }

    /**
     * 6. GET /cabang/{uuid}/warga — Daftar Warga di Cabang Tertentu
     */
    public function getCabangWarga(string $cabangUuid, int $page = 1, int $perPage = 25, ?string $gender = null): array
    {
        $query = [
            'page'     => max(1, $page),
            'per_page' => min(max(1, $perPage), 100),
        ];

        if (!empty($gender) && in_array(strtoupper($gender), ['L', 'P'], true)) {
            $query['kelamin'] = strtoupper($gender);
        }

        return $this->request('GET', 'cabang/' . urlencode($cabangUuid) . '/warga', ['query' => $query]);
    }

    /**
     * Dapatkan UUID Perwakilan Sragen
     */
    public function getSragenUuid(): string
    {
        return $this->config->perwakilanUuid;
    }

    /**
     * Detail Perwakilan Sragen beserta seluruh daftar cabangnya
     */
    public function getPerwakilanSragenDetail(): array
    {
        return $this->getPerwakilanDetail($this->getSragenUuid());
    }

    /**
     * Daftar seluruh cabang khusus Perwakilan Sragen
     */
    public function getCabangSragenList(): array
    {
        $detail = $this->getPerwakilanSragenDetail();
        if (($detail['success'] ?? false) && isset($detail['data']['cabang'])) {
            return [
                'success' => true,
                'data'    => $detail['data']['cabang'],
                'total'   => count($detail['data']['cabang']),
            ];
        }

        return $this->getCabangList($this->getSragenUuid(), null, 200);
    }

    /**
     * 7. GET /warga — Daftar Warga (Pagination & Filter Lengkap)
     */
    public function getWargaList(array $params = []): array
    {
        $allowedParams = ['page', 'per_page', 'search', 'kelamin', 'status', 'perwakilan', 'cabang', 'order_by', 'direction'];
        $query = [];

        foreach ($allowedParams as $param) {
            if (isset($params[$param]) && $params[$param] !== '') {
                $query[$param] = $params[$param];
            }
        }

        // Default scope ke Perwakilan Sragen jika tidak ditentukan
        if (!isset($query['perwakilan']) && !isset($query['cabang'])) {
            $query['perwakilan'] = $this->getSragenUuid();
        }

        return $this->request('GET', 'warga', ['query' => $query]);
    }

    /**
     * 8. GET /warga/search — Pencarian Cepat Warga (Default: Khusus Perwakilan Sragen)
     */
    public function searchWarga(string $query, array $params = []): array
    {
        $queryParams = [
            'q' => trim($query),
        ];

        if (!empty($params['limit'])) {
            $queryParams['limit'] = min(max(1, (int) $params['limit']), 50);
        }
        if (!empty($params['kelamin'])) {
            $queryParams['kelamin'] = $params['kelamin'];
        }
        if (!empty($params['cabang'])) {
            $queryParams['cabang'] = $params['cabang'];
        }
        // Kunci default ke Perwakilan Sragen
        $queryParams['perwakilan'] = !empty($params['perwakilan']) ? $params['perwakilan'] : $this->getSragenUuid();

        return $this->request('GET', 'warga/search', ['query' => $queryParams]);
    }

    /**
     * 9. GET /warga/{uuid} — Detail Lengkap Warga Berdasarkan UUID
     */
    public function getWargaDetail(string $uuid): array
    {
        return $this->request('GET', 'warga/' . urlencode($uuid));
    }
}
