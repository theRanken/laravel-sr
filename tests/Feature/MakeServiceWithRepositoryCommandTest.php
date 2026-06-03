<?php

namespace Ranken\ServiceRepositoryGenerator\Tests\Feature;

use Ranken\ServiceRepositoryGenerator\Tests\TestCase;

class MakeServiceWithRepositoryCommandTest extends TestCase
{
    public function test_it_generates_service_and_repository_files_and_updates_providers(): void
    {
        $namespace = trim(app()->getNamespace(), '\\');

        $this->artisan('service-repository:install')
            ->assertExitCode(0);

        $this->artisan('make:service User --repository')
            ->assertExitCode(0);

        $this->assertFileExists(app_path('Services/User/UserServiceInterface.php'));
        $this->assertFileExists(app_path('Services/User/UserService.php'));
        $this->assertFileExists(app_path('Repositories/User/UserRepositoryInterface.php'));
        $this->assertFileExists(app_path('Repositories/User/UserRepository.php'));

        $serviceProvider = file_get_contents(app_path('Providers/ServicePatternProvider.php'));
        $repositoryProvider = file_get_contents(app_path('Providers/RepositoryPatternProvider.php'));

        $this->assertStringContainsString("\$this->app->bind(\\{$namespace}\\Services\\User\\UserServiceInterface::class, \\{$namespace}\\Services\\User\\UserService::class);", $serviceProvider);
        $this->assertStringContainsString("\$this->app->bind(\\{$namespace}\\Repositories\\User\\UserRepositoryInterface::class, \\{$namespace}\\Repositories\\User\\UserRepository::class);", $repositoryProvider);
    }

    public function test_it_does_not_duplicate_existing_bindings(): void
    {
        $this->artisan('service-repository:install')
            ->assertExitCode(0);

        $this->artisan('make:service Report --repository')
            ->assertExitCode(0);

        $this->artisan('make:service Report --repository')
            ->assertExitCode(0);

        $serviceProvider = file_get_contents(app_path('Providers/ServicePatternProvider.php'));
        $repositoryProvider = file_get_contents(app_path('Providers/RepositoryPatternProvider.php'));

        $this->assertSame(1, substr_count($serviceProvider, 'ReportServiceInterface::class'));
        $this->assertSame(1, substr_count($repositoryProvider, 'ReportRepositoryInterface::class'));
    }
}
