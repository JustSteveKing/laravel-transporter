<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Tests\Stubs;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Client\PendingRequest;
use JustSteveKing\Transporter\Request;

class PostRequest extends Request
{
    protected string $method = 'GET';
    protected string $baseUrl = 'https://jsonplaceholder.typicode.com';
    protected string $path = '/posts';

    public function fakeResponse(PendingRequest $request): Response
    {
        return new Response(
            body: json_encode([
                "userId" => 100,
                "id" => 1,
                "title" => "sunt aut facere repellat provident occaecati excepturi optio reprehenderit",
                "body" => "quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto"
            ])
        );
    }
}
