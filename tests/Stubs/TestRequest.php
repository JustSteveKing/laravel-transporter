<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Tests\Stubs;

use JustSteveKing\Transporter\Concerns\HandlesUri;
use JustSteveKing\Transporter\Concerns\HasHeaders;
use JustSteveKing\Transporter\Concerns\HasPayload;
use JustSteveKing\Transporter\Concerns\ForwardsRequests;
use JustSteveKing\Transporter\Contracts\RequestContract;
use JustSteveKing\Transporter\Concerns\HandlesClientSetup;
use JustSteveKing\Transporter\Concerns\HandlesAuthentication;

class TestRequest implements RequestContract
{
    use HandlesUri;
    use HasPayload;
    use HasHeaders;
    use ForwardsRequests;
    use HandlesClientSetup;
    use HandlesAuthentication;
    
    public string $path = 'posts';

    public array $payload = [];

    public array $parameters = [];

    public array $headers = [
        'Accept' => 'application/json'
    ];

    public string $baseUri = 'https://jsonplaceholder.typicode.com';

    public function method(): string
    {
        return 'GET';
    }
}
