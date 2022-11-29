<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Tests\Stubs;

use JustSteveKing\Transporter\Request;

class ThrowingRequest extends Request
{
    protected string $method = 'GET';
    protected string $baseUrl = 'https://jsonplaceholder.typicode.com';
    protected string $path = '/todos';
    protected bool $throws = true;
}
