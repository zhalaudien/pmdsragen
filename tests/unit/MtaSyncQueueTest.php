<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\MtaSyncQueueModel;
use App\Services\MtaSyncService;
use App\Controllers\Admin\MtaSync;
use App\Commands\MtaSyncQueue;
use Config\Services;

/**
 * @internal
 */
final class MtaSyncQueueTest extends CIUnitTestCase
{
    public function testClassesAndModelsExist(): void
    {
        $this->assertTrue(class_exists(MtaSyncQueueModel::class));
        $this->assertTrue(class_exists(MtaSyncService::class));
        $this->assertTrue(class_exists(MtaSync::class));
        $this->assertTrue(class_exists(MtaSyncQueue::class));
    }

    public function testQueueModelStructure(): void
    {
        $reflection = new \ReflectionClass(MtaSyncQueueModel::class);
        $this->assertTrue($reflection->hasMethod('getQueueSummary'));
        $this->assertTrue($reflection->hasMethod('getNextPendingItem'));
        $this->assertTrue($reflection->hasMethod('getRecentProcessed'));
        $this->assertTrue($reflection->hasMethod('clearPendingQueue'));
        $this->assertTrue($reflection->hasMethod('resetFailedQueue'));

        $defaultProps = $reflection->getDefaultProperties();
        $this->assertEquals('mta_sync_queue', $defaultProps['table']);
        $this->assertEquals('id', $defaultProps['primaryKey']);
        $this->assertContains('pemuda_id', $defaultProps['allowedFields']);
        $this->assertContains('cabang_id', $defaultProps['allowedFields']);
        $this->assertContains('status', $defaultProps['allowedFields']);
        $this->assertContains('result', $defaultProps['allowedFields']);
        $this->assertContains('attempts', $defaultProps['allowedFields']);
    }

    public function testSyncServiceQueueMethods(): void
    {
        $reflection = new \ReflectionClass(MtaSyncService::class);
        $this->assertTrue($reflection->hasMethod('initSyncQueue'));
        $this->assertTrue($reflection->hasMethod('processNextQueueItem'));
        $this->assertTrue($reflection->hasMethod('getQueueStatus'));
        $this->assertTrue($reflection->hasMethod('cancelQueue'));
        $this->assertTrue($reflection->hasMethod('syncAndVerifyAllPemudaSragen'));

        // Pastikan parameter initSyncQueue benar
        $initMethod = $reflection->getMethod('initSyncQueue');
        $params = $initMethod->getParameters();
        $this->assertEquals('cabangId', $params[0]->getName());
        $this->assertEquals('onlyPending', $params[1]->getName());
    }

    public function testControllerHasQueueMethods(): void
    {
        $reflection = new \ReflectionClass(MtaSync::class);
        $this->assertTrue($reflection->hasMethod('queueInit'));
        $this->assertTrue($reflection->hasMethod('queueProcessItem'));
        $this->assertTrue($reflection->hasMethod('queueStatus'));
        $this->assertTrue($reflection->hasMethod('queueCancel'));
        $this->assertTrue($reflection->hasMethod('syncVerifyAll'));
    }

    public function testRoutesAreProperlyConfigured(): void
    {
        $routes = Services::routes();
        require APPPATH . 'Config/Routes.php';

        $getRoutes  = $routes->getRoutes('GET');
        $postRoutes = $routes->getRoutes('POST');

        $this->assertArrayHasKey('admin/mta-sync/queue-status', $getRoutes);
        $this->assertArrayHasKey('admin/mta-sync/queue-init', $postRoutes);
        $this->assertArrayHasKey('admin/mta-sync/queue-process-item', $postRoutes);
        $this->assertArrayHasKey('admin/mta-sync/queue-cancel', $postRoutes);
        $this->assertArrayHasKey('admin/mta-sync/sync-verify-all', $postRoutes);

        $this->assertEquals('\App\Controllers\Admin\MtaSync::queueStatus', $getRoutes['admin/mta-sync/queue-status']);
        $this->assertEquals('\App\Controllers\Admin\MtaSync::queueInit', $postRoutes['admin/mta-sync/queue-init']);
        $this->assertEquals('\App\Controllers\Admin\MtaSync::queueProcessItem', $postRoutes['admin/mta-sync/queue-process-item']);
        $this->assertEquals('\App\Controllers\Admin\MtaSync::queueCancel', $postRoutes['admin/mta-sync/queue-cancel']);
    }

    public function testViewContainsQueueUiAndPacing(): void
    {
        $viewPath = APPPATH . 'Views/admin/mta_sync/index.php';
        $this->assertFileExists($viewPath);

        $content = file_get_contents($viewPath);

        // Periksa teks dan indikator laju 40 data / menit
        $this->assertStringContainsString('40 Data / Menit', $content);
        $this->assertStringContainsString('QUEUE_DELAY_MS = 1500', $content);
        $this->assertStringContainsString('modalSyncVerifyAll', $content);
        $this->assertStringContainsString('queueProgressBar', $content);
        $this->assertStringContainsString('queueCooldownAlert', $content);
        $this->assertStringContainsString('btnPauseResumeQueue', $content);
        $this->assertStringContainsString('btnStopQueue', $content);
        $this->assertStringContainsString('admin/mta-sync/queue-process-item', $content);
    }
}
