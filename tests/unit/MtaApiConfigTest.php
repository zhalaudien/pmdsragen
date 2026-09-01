<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Config\MtaApi;
use App\Services\MtaApiService;

/**
 * @internal
 */
final class MtaApiConfigTest extends CIUnitTestCase
{
    public function testMtaApiConfigLoadsProperties(): void
    {
        $config = new MtaApi();

        $this->assertNotEmpty($config->baseUrl);
        $this->assertIsString($config->apiToken);
        $this->assertIsInt($config->timeout);
        $this->assertIsBool($config->enabled);
        $this->assertEquals('3246792b-f0a7-48ca-95fa-379e3bee777d', $config->perwakilanUuid);
        $this->assertEquals('Sragen', $config->perwakilanNama);
        $this->assertEquals('86', $config->perwakilanKode);
    }

    public function testMtaApiReadsTokenFromEnvironment(): void
    {
        $originalEnv = $_ENV['MTA_API_TOKEN'] ?? null;
        $_ENV['MTA_API_TOKEN'] = 'test_token_from_env_12345';
        $_SERVER['MTA_API_TOKEN'] = 'test_token_from_env_12345';

        $config = new MtaApi();
        $this->assertEquals('test_token_from_env_12345', $config->apiToken);

        $service = new MtaApiService($config);
        $this->assertTrue($service->isEnabled());

        // Cleanup
        if ($originalEnv !== null) {
            $_ENV['MTA_API_TOKEN'] = $originalEnv;
            $_SERVER['MTA_API_TOKEN'] = $originalEnv;
        } else {
            unset($_ENV['MTA_API_TOKEN'], $_SERVER['MTA_API_TOKEN']);
        }
    }
}
