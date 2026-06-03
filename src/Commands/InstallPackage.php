<?php

namespace Ranken\ServiceRepositoryGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class InstallPackage extends Command
{
    protected $signature = 'service-repository:install';

    protected $description = 'Install the Service Repository package';

    public function handle()
    {
        $this->info('Installing Service Repository Package...');

        $this->publishConfiguration();
        $this->publishProviders();
        $this->registerProviders();

        $this->info('Service Repository Package installed successfully.');
    }

    protected function publishConfiguration()
    {
        if (!File::exists(config_path('service-repo.php'))) {
            $this->call('vendor:publish', [
                '--provider' => "Ranken\ServiceRepositoryGenerator\ServiceRepositoryServiceProvider",
                '--tag' => "config"
            ]);
        } else {
            if ($this->confirm('The service-repo configuration file already exists. Do you want to overwrite it?')) {
                $this->call('vendor:publish', [
                    '--provider' => "Ranken\ServiceRepositoryGenerator\ServiceRepositoryServiceProvider",
                    '--tag' => "config",
                    '--force' => true
                ]);
            }
        }
    }

    protected function publishProviders()
    {
        $this->call('vendor:publish', [
            '--provider' => "Ranken\ServiceRepositoryGenerator\ServiceRepositoryServiceProvider",
            '--tag' => "providers"
        ]);
    }

    protected function registerProviders()
    {
        $providers = [
            'App\\Providers\\ServicePatternProvider',
            'App\\Providers\\RepositoryPatternProvider',
        ];

        if ($this->registerProvidersInBootstrapFile($providers)) {
            return;
        }

        $this->registerProvidersInConfig($providers);
    }

    protected function registerProvidersInBootstrapFile(array $providers): bool
    {
        if (! method_exists(ServiceProvider::class, 'addProviderToBootstrapFile')) {
            return false;
        }

        $bootstrapProvidersPath = base_path('bootstrap/providers.php');

        if (! File::exists($bootstrapProvidersPath)) {
            return false;
        }

        foreach ($providers as $provider) {
            ServiceProvider::addProviderToBootstrapFile($provider);
        }

        return true;
    }

    protected function registerProvidersInConfig(array $providers): void
    {
        $appConfigPath = config_path('app.php');

        if (! File::exists($appConfigPath)) {
            $this->warn('Unable to locate config/app.php for provider registration.');
            return;
        }

        $appConfig = File::get($appConfigPath);

        foreach ($providers as $provider) {
            $classReference = $provider.'::class';

            if (str_contains($appConfig, $classReference)) {
                continue;
            }

            $updatedConfig = preg_replace(
                "/(ServiceProvider::defaultProviders\\(\\)->merge\\(\\[\\s*)/m",
                "$1        {$classReference},\n",
                $appConfig,
                1,
                $replacements
            );

            if ($replacements === 1) {
                $appConfig = $updatedConfig;
                continue;
            }

            $updatedConfig = preg_replace(
                "/('providers'\\s*=>\\s*\\[\\s*)/m",
                "$1        {$classReference},\n",
                $appConfig,
                1,
                $replacements
            );

            if ($replacements !== 1 || $updatedConfig === null) {
                $this->warn("Unable to register {$classReference} automatically in config/app.php.");
                continue;
            }

            $appConfig = $updatedConfig;
        }

        File::put($appConfigPath, $appConfig);
    }
}
