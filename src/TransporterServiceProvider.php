<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TransporterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name(
            name: 'transporter',
        );
    }
}
