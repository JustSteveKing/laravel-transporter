<?php

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Artisan;
use JustSteveKing\StatusCode\Http;
use JustSteveKing\Transporter\Commands\TransporterCommand;
use JustSteveKing\Transporter\Tests\Stubs\BaseUriRequest;
use JustSteveKing\Transporter\Tests\Stubs\PostRequest;
use JustSteveKing\Transporter\Tests\Stubs\TestRequest;

it('can create a pending request', function () {
    expect(TestRequest::fake()->getRequest())
        ->toBeInstanceOf(PendingRequest::class);
});

it('can send a request', function () {
    expect(TestRequest::fake()->setPath(
        path: '/todos/1',
    )->send()->json())->toEqual([]);
});

it('can add query parameters', function () {
    $response = TestRequest::fake()->setPath(
        path: '/comments',
    )->withQuery(
        query: [
                   'postId' => 1,
               ],
    )->withFakeData([
        [
            "postId"=> 1,
            "id"=> 1,
            "name"=> "id labore ex et quam laborum",
            "email"=> "Eliseo@gardner.biz",
            "body"=> "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium"
        ],
        [
            "postId"=> 1,
            "id"=> 2,
            "name"=> "quo vero reiciendis velit similique earum",
            "email"=> "Jayne_Kuhic@sydney.com",
            "body"=> "est natus enim nihil est dolore omnis voluptatem numquam\net omnis occaecati quod ullam at\nvoluptatem error expedita pariatur\nnihil sint nostrum voluptatem reiciendis et"
        ],
        [
            "postId"=> 1,
            "id"=> 3,
            "name"=> "odio adipisci rerum aut animi",
            "email"=> "Nikita@garfield.biz",
            "body"=> "quia molestiae reprehenderit quasi aspernatur\naut expedita occaecati aliquam eveniet laudantium\nomnis quibusdam delectus saepe quia accusamus maiores nam est\ncum et ducimus et vero voluptates excepturi deleniti ratione"
        ]
    ])->send();

    expect(
        $response->json()
    )->toHaveCount(3)->toBeArray();

    foreach ($response->json() as $item) {
        expect($item['postId'])->toBe(1);
    }
});

it('can add data to the request', function () {
    $data = [
        'title' => 'transporter test',
        'body' => 'transporter test',
        'userId' => 1,
    ];

    expect(
        PostRequest::fake()->withData(
            data: $data
        )->send()->status(),
    )->toEqual(Http::OK);
});

it('can create a new api request using the command', function () {
    expect(
        file_exists(
            filename: __DIR__ . '/../../stubs/api-request.stub',
        )
    )->toBeTrue();

    Artisan::call(
        command: TransporterCommand::class,
        parameters: ['name' => 'TestRequest'],
    );

    expect(
        file_exists(
            filename: __DIR__ . '/../../vendor/orchestra/testbench-core/laravel/app/Transporter/Requests/TestRequest.php'
        )
    )->toBeTrue();
});

it('can create a fake response', function () {
    expect(
        PostRequest::fake()->send()->json('userId')
    )->toEqual(100);
});

it('can set a base uri using env and config', function () {
    expect(
        PostRequest::fake()->getBaseUrl(),
    )->toEqual('https://jsonplaceholder.typicode.com');

    config([
        'transporter' => [
            'base_uri' => 'https://example.com'
        ]
    ]);

    expect(
        BaseUriRequest::fake()->getBaseUrl()
    )->toEqual('https://example.com');
});

it('can set the response status on fake requests', function () {
    expect(
        TestRequest::fake()->send()->status()
    )->toEqual(Http::OK);

    expect(
        TestRequest::fake(
            status: Http::ACCEPTED
        )->send()->status()
    )->toEqual(Http::ACCEPTED);
});

it('can add query parameters recursively without overwriting', function () {
    $query = TestRequest::fake()
        ->withQuery(
            query: [
                       'postId' => 1,
                   ],
        )->withQuery(
            query: [
                        'page' => [
                            'number' => 2,
                        ],
                    ],
        )->withQuery(
            query: [
                        'page' => [
                            'size' => 30,
                        ],
                    ],
        )->getQuery();

    expect(
        $query
    )->toBeArray()->toHaveCount(2);

    expect(
        $query['page']
    )->toBeArray()->toHaveCount(2);

    expect(
        $query['page']['number']
    )->toBe(2);

    expect(
        $query['page']['size']
    )->toBe(30);
});
