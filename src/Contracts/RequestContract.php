<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Contracts;

use JustSteveKing\UriBuilder\Uri;

interface RequestContract
{
    public function retry(): int;

    public function retryTiming(): float;

    public function timeout(): float;

    public function requiresAuth(): bool;

    public function authStrategy(): string|null;

    public function authCredentials(): array|null;

    public function method(): string;

    public function headers(array $headers = []): array;
    
    public function payload(array $payload = []): array;
    
    public function uri(): Uri;

    public function path(string | null $path = null): null | string;

    public function parameters(array $parameters = []): array;

    public function fragment(): null | string;
}
