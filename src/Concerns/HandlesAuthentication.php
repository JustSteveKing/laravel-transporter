<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Concerns;

trait HandlesAuthentication
{
    public function requiresAuth(): bool
    {
        return false;
    }

    public function authStrategy(): string|null
    {
        return null;
    }

    public function authCredentials(): array|null
    {
        return null;
    }
}
