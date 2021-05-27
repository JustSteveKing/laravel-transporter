<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Concerns;

trait HasPayload
{
    public function payload(array $payload = []): array
    {
        $body = array_merge($payload, []);

        return [
            'body' => $body
        ];
    }
}
