<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Concerns;

use Illuminate\Http\Client\Response;
use JustSteveKing\Transporter\Transporter;

trait ForwardsRequests
{
    public static function __callStatic($name, ...$args): Response
    {
        return Transporter::request(
            request: new static(),
        )->$name(...$args)->send();
    }
}
