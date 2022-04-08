<?php

namespace Idez\Bankly;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Idez\Bankly\Commands\BanklyCommand;

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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-bankly-sdk_table')
            ->hasCommand(BanklyCommand::class);
    }
}
