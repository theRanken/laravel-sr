<?php

namespace Ranken\ServiceRepositoryGenerator\Tests;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase as Orchestra;
use Ranken\ServiceRepositoryGenerator\ServiceRepositoryServiceProvider;

abstract class TestCase extends Orchestra
{
    protected Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem();

        $this->files->ensureDirectoryExists(app_path('Providers'));
        $this->files->ensureDirectoryExists(app_path('Services'));
        $this->files->ensureDirectoryExists(app_path('Repositories'));
        $this->files->ensureDirectoryExists(config_path());
        $this->files->ensureDirectoryExists(base_path('bootstrap'));
    }

    protected function tearDown(): void
    {
        $this->files->delete(app_path('Providers/ServicePatternProvider.php'));
        $this->files->delete(app_path('Providers/RepositoryPatternProvider.php'));
        $this->files->deleteDirectory(app_path('Services'));
        $this->files->deleteDirectory(app_path('Repositories'));
        $this->files->delete(config_path('service-repo.php'));
        $this->files->delete(config_path('app.php'));
        $this->files->delete(base_path('bootstrap/providers.php'));

        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [ServiceRepositoryServiceProvider::class];
    }
}
