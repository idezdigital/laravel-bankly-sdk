<?php

namespace Idez\Bankly;

use Idez\Bankly\Enums\Commands\BanklyCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BanklyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-bankly-sdk')
            ->hasConfigFile('bankly')
            ->hasViews()
            ->hasMigration('create_laravel-bankly-sdk_table')
            ->hasCommand(BanklyCommand::class);
    }
}
