<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Concerns;

use JustSteveKing\Transporter\Transporter;

trait ForwardsRequests
{
    public static function __callStatic(string $name, array $args): Transporter
    {
        return Transporter::request(
            request: new static(),
        )->$name(...$args);
    }
}
