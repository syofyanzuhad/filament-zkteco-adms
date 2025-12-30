<?php

namespace Syofyanzuhad\FilamentZktecoAdms;

use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Syofyanzuhad\FilamentZktecoAdms\Commands\FilamentZktecoAdmsCommand;
use Syofyanzuhad\FilamentZktecoAdms\Testing\TestsFilamentZktecoAdms;

class FilamentZktecoAdmsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-zkteco-adms';

    public static string $viewNamespace = 'filament-zkteco-adms';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('syofyanzuhad/filament-zkteco-adms');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-zkteco-adms/{$file->getFilename()}"),
                ], 'filament-zkteco-adms-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsFilamentZktecoAdms);

        // Register ADMS routes
        $this->registerAdmsRoutes();
    }

    protected function registerAdmsRoutes(): void
    {
        $routeConfig = config('zkteco-adms.routes', []);

        Route::group([
            'prefix' => $routeConfig['prefix'] ?? 'iclock',
            'middleware' => $routeConfig['middleware'] ?? ['api'],
            'domain' => $routeConfig['domain'] ?? null,
            'as' => 'zkteco-adms.',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/adms.php');
        });
    }

    protected function getAssetPackageName(): ?string
    {
        return 'syofyanzuhad/filament-zkteco-adms';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentZktecoAdmsCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            '2024_01_01_000001_create_zkteco_devices_table',
            '2024_01_01_000002_create_zkteco_attendance_logs_table',
            '2024_01_01_000003_create_zkteco_users_table',
            '2024_01_01_000004_create_zkteco_device_commands_table',
        ];
    }
}
