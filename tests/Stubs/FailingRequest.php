<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Tests\Stubs;

use JustSteveKing\Transporter\Concerns\ForwardsRequests;
use JustSteveKing\Transporter\Concerns\HandlesClientSetup;
use JustSteveKing\Transporter\Concerns\HandlesUri;
use JustSteveKing\Transporter\Concerns\HasHeaders;
use JustSteveKing\Transporter\Concerns\HasPayload;
use JustSteveKing\Transporter\Contracts\RequestContract;

class FailingRequest implements RequestContract
{
    use HandlesUri;
    use HasPayload;
    use HasHeaders;
    use ForwardsRequests;
    use HandlesClientSetup;
    
    public string $path = 'postsss';

    public string $baseUri = 'https://jsonplaceholder.typicode.com';

    public function requiresAuth(): bool
    {
        return false;
    }

    public function authStrategy(): string | null
    {
        return null;
    }

    public function authCredentials(): string | array | null
    {
        return null;
    }

    public function method(): string
    {
        return 'GET';
    }
}
