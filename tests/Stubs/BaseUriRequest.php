<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Tests\Stubs;

use JustSteveKing\Transporter\Request;

class BaseUriRequest extends Request
{
    protected string $method = 'GET';
    protected string $path = '/todos';
}
