<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class MtaApi extends BaseConfig
{
    /**
     * Base URL endpoint MTA Database Warga API v1
     */
    public string $baseUrl = 'https://api.mta.or.id/api/v1';

    /**
     * API Token untuk autentikasi (Header X-API-Token / Authorization: Bearer)
     * Wajib diambil dari environment variable MTA_API_TOKEN di .env
     */
    public string $apiToken = '';

    /**
     * Request Timeout dalam detik
     */
    public int $timeout = 15;

    /**
     * Rate limit request per menit sesuai ketentuan API MTA
     */
    public int $rateLimitPerMinute = 60;

    /**
     * Status aktif integrasi API MTA
     */
    public bool $enabled = true;

    /**
     * Scope Khusus Perwakilan Sragen
     */
    public string $perwakilanUuid = '3246792b-f0a7-48ca-95fa-379e3bee777d';
    public string $perwakilanNama = 'Sragen';
    public string $perwakilanKode = '86';

    public function __construct()
    {
        parent::__construct();

        // Ambil konfigurasi dinamis dari .env
        $envUrl = env('MTA_API_BASE_URL', getenv('MTA_API_BASE_URL'));
        if ($envUrl) {
            $this->baseUrl = rtrim($envUrl, '/');
        }

        $envToken = env('MTA_API_TOKEN', getenv('MTA_API_TOKEN'));
        if ($envToken !== null && $envToken !== '') {
            $this->apiToken = (string) $envToken;
        }

        $envTimeout = env('MTA_API_TIMEOUT', getenv('MTA_API_TIMEOUT'));
        if ($envTimeout !== null && $envTimeout !== '') {
            $this->timeout = (int) $envTimeout;
        }

        $envEnabled = env('MTA_API_ENABLED', getenv('MTA_API_ENABLED'));
        if ($envEnabled !== null && $envEnabled !== '') {
            $this->enabled = filter_var($envEnabled, FILTER_VALIDATE_BOOLEAN);
        }

        $envPerwakilanUuid = env('MTA_PERWAKILAN_UUID', getenv('MTA_PERWAKILAN_UUID'));
        if ($envPerwakilanUuid) {
            $this->perwakilanUuid = $envPerwakilanUuid;
        }

        $envPerwakilanNama = env('MTA_PERWAKILAN_NAMA', getenv('MTA_PERWAKILAN_NAMA'));
        if ($envPerwakilanNama) {
            $this->perwakilanNama = $envPerwakilanNama;
        }
    }
}
