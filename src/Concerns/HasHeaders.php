<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Concerns;

trait HasHeaders
{
    public function headers(array $headers = []): array
    {
        return array_merge($headers, [
            'Accept' => 'application/json',
        ]);
    }
}
