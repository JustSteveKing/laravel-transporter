<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter;

use Illuminate\Http\Client\Factory;

class Transporter extends Factory
{
    public static function build(callable $callback): array
    {
        return (new self)->pool(
            callback: $callback,
        );
    }
}
