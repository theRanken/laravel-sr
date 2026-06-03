<?php

namespace Ranken\ServiceRepositoryGenerator\Tests\Feature;

use Illuminate\Support\ServiceProvider;
use Ranken\ServiceRepositoryGenerator\Tests\TestCase;

class InstallPackageCommandTest extends TestCase
{
    public function test_it_registers_providers_in_bootstrap_file_when_supported(): void
    {
        if (! method_exists(ServiceProvider::class, 'addProviderToBootstrapFile')) {
            $this->markTestSkipped('Bootstrap provider registration is not available on this Laravel version.');
        }

        file_put_contents(base_path('bootstrap/providers.php'), <<<'PHP'
<?php

return [
];
PHP);

        $this->artisan('service-repository:install')
            ->assertExitCode(0);

        $bootstrapProviders = file_get_contents(base_path('bootstrap/providers.php'));

        $this->assertStringContainsString('App\\Providers\\ServicePatternProvider::class', $bootstrapProviders);
        $this->assertStringContainsString('App\\Providers\\RepositoryPatternProvider::class', $bootstrapProviders);
        $this->assertFileExists(app_path('Providers/ServicePatternProvider.php'));
        $this->assertFileExists(app_path('Providers/RepositoryPatternProvider.php'));
        $this->assertFileExists(config_path('service-repo.php'));
    }

    public function test_it_falls_back_to_config_app_provider_registration(): void
    {
        file_put_contents(config_path('app.php'), <<<'PHP'
<?php

return [
    'providers' => [
    ],
];
PHP);

        $this->artisan('service-repository:install')
            ->assertExitCode(0);

        $appConfig = file_get_contents(config_path('app.php'));

        $this->assertStringContainsString('App\\Providers\\ServicePatternProvider::class', $appConfig);
        $this->assertStringContainsString('App\\Providers\\RepositoryPatternProvider::class', $appConfig);
        $this->assertFileExists(app_path('Providers/ServicePatternProvider.php'));
        $this->assertFileExists(app_path('Providers/RepositoryPatternProvider.php'));
    }
}
