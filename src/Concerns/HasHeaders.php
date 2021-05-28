<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Concerns;

trait HasHeaders
{
    public function headers(array $headers = []): array
    {
        $this->headers = array_merge($headers, $this->headers);

        return $this->headers;
    }
}
