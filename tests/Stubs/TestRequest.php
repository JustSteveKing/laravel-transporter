<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Tests\Stubs;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Client\PendingRequest;
use JustSteveKing\Transporter\Request;

class TestRequest extends Request
{
    protected string $method = 'GET';
    protected string $baseUrl = 'https://jsonplaceholder.typicode.com';
    protected string $path = '/todos';

    protected array $data = [
        'completed' => false,
    ];

    protected ?array $fakeData = null;

    protected function withRequest(PendingRequest $request): void
    {
        $request->withToken('foobar');
    }

    public function withFakeData(array $data): static
    {
        $this->fakeData = $data;

        return $this;
    }

    public function fakeResponse(): Response
    {
        return new Response(
            body: json_encode($this->fakeData ?? [
                "userId" => 1,
                "id" => 1,
                "title" => "delectus aut autem",
                "completed" => false
            ])
        );
    }
}
