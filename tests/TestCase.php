<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;
use Syofyanzuhad\FilamentZktecoAdms\FilamentZktecoAdmsServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Syofyanzuhad\\FilamentZktecoAdms\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentZktecoAdmsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        // Set app key for encryption
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));

        // Load package config
        $app['config']->set('zkteco-adms', require __DIR__ . '/../config/zkteco-adms.php');

        // Use no middleware for testing
        $app['config']->set('zkteco-adms.routes.middleware', []);

        // Run all package migrations
        $migrations = [
            '2024_01_01_000001_create_zkteco_devices_table.php.stub',
            '2024_01_01_000002_create_zkteco_attendance_logs_table.php.stub',
            '2024_01_01_000003_create_zkteco_users_table.php.stub',
            '2024_01_01_000004_create_zkteco_device_commands_table.php.stub',
        ];

        foreach ($migrations as $migration) {
            $migrationClass = include __DIR__ . '/../database/migrations/' . $migration;
            $migrationClass->up();
        }
    }
}
