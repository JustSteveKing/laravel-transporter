<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Concerns;

trait HasPayload
{
    public function payload(array $payload = []): array
    {
        return array_merge($payload, $this->payload);

        return $this->payload;
    }
}
