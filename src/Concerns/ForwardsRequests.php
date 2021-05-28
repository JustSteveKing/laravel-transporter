<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Concerns;

use JustSteveKing\Transporter\Transporter;

/**
 * @method static \JustSteveKing\Transporter\Transporter to(?string $path)
 * @method static \JustSteveKing\Transporter\Transporter with(array $payload, array $headers)
 */
trait ForwardsRequests
{
    public static function __callStatic(string $name, array $args): Transporter
    {
        return Transporter::request(
            request: new static(),
        )->$name(...$args);
    }
}
