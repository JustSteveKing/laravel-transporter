<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Commands;

use Illuminate\Console\GeneratorCommand;

class TransporterCommand extends GeneratorCommand
{
    public $signature = 'make:api-request {name}';

    public $description = 'Create an new API Request to use with Transporter.';

    protected $types = 'class';

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/api-request.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Transporter\Requests';
    }
}
