<?php

namespace Spatie\DynamicServers;

use Spatie\DynamicServers\Commands\ListDynamicServersCommand;
use Spatie\DynamicServers\Commands\MonitorDynamicServersCommand;
use Spatie\DynamicServers\Support\DynamicServers;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DynamicServersServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-dynamic-servers')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_dynamic_servers_table')
            ->hasCommands(
                ListDynamicServersCommand::class,
                MonitorDynamicServersCommand::class,
            );
    }

    public function packageRegistered()
    {
        $this->app->singleton(DynamicServers::class, fn () => new DynamicServers());

        $this->app->bind('dynamicServers', DynamicServers::class);
    }
}
