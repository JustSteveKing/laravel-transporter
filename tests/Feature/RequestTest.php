<?php

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Artisan;
use JustSteveKing\StatusCode\Http;
use JustSteveKing\Transporter\Facades\Concurrently;
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

it('can send a request using energize', function () {
    expect(TestRequest::fake()->setPath(
        path: '/todos/1',
    )->energize()->json())->toEqual([]);
});

it('can run concurrent requests', function () {
    $responses = Concurrently::fake()->setRequests([
        TestRequest::fake()->withQuery(
            query: [
                'postId' => 1,
            ],
        )->setPath(
            path: '/comments',
        )->withFakeData([
            [
                'postId' => 1,
                'id' => 1,
                'name' => 'id labore ex et quam laborum',
                'email' => 'Eliseo@gardner.biz',
                'body' => "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium",
            ],
            [
                'postId' => 1,
                'id' => 2,
                'name' => 'quo vero reiciendis velit similique earum',
                'email' => 'Jayne_Kuhic@sydney.com',
                'body' => "est natus enim nihil est dolore omnis voluptatem numquam\net omnis occaecati quod ullam at\nvoluptatem error expedita pariatur\nnihil sint nostrum voluptatem reiciendis et",
            ],
            [
                'postId' => 1,
                'id' => 3,
                'name' => 'odio adipisci rerum aut animi',
                'email' => 'Nikita@garfield.biz',
                'body' => "quia molestiae reprehenderit quasi aspernatur\naut expedita occaecati aliquam eveniet laudantium\nomnis quibusdam delectus saepe quia accusamus maiores nam est\ncum et ducimus et vero voluptates excepturi deleniti ratione",
            ],
        ])->as('first'),
        TestRequest::fake()->withQuery(
            query: [
                'postId' => 2,
            ],
        )->setPath(
            path: '/comments',
        )->withFakeData([
            [
                'postId' => 2,
                'id' => 4,
                'name' => 'id labore ex et quam laborum',
                'email' => 'Eliseo@gardner.biz',
                'body' => "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium",
            ],
            [
                'postId' => 2,
                'id' => 5,
                'name' => 'quo vero reiciendis velit similique earum',
                'email' => 'Jayne_Kuhic@sydney.com',
                'body' => "est natus enim nihil est dolore omnis voluptatem numquam\net omnis occaecati quod ullam at\nvoluptatem error expedita pariatur\nnihil sint nostrum voluptatem reiciendis et",
            ],
            [
                'postId' => 2,
                'id' => 6,
                'name' => 'odio adipisci rerum aut animi',
                'email' => 'Nikita@garfield.biz',
                'body' => "quia molestiae reprehenderit quasi aspernatur\naut expedita occaecati aliquam eveniet laudantium\nomnis quibusdam delectus saepe quia accusamus maiores nam est\ncum et ducimus et vero voluptates excepturi deleniti ratione",
            ],
        ])->as('second'),
        TestRequest::fake()->withQuery(
            query: [
                'postId' => 3,
            ],
        )->setPath(
            path: '/comments',
        )->withFakeData([
            [
                'postId' => 3,
                'id' => 7,
                'name' => 'id labore ex et quam laborum',
                'email' => 'Eliseo@gardner.biz',
                'body' => "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium",
            ],
            [
                'postId' => 3,
                'id' => 8,
                'name' => 'quo vero reiciendis velit similique earum',
                'email' => 'Jayne_Kuhic@sydney.com',
                'body' => "est natus enim nihil est dolore omnis voluptatem numquam\net omnis occaecati quod ullam at\nvoluptatem error expedita pariatur\nnihil sint nostrum voluptatem reiciendis et",
            ],
            [
                'postId' => 3,
                'id' => 9,
                'name' => 'odio adipisci rerum aut animi',
                'email' => 'Nikita@garfield.biz',
                'body' => "quia molestiae reprehenderit quasi aspernatur\naut expedita occaecati aliquam eveniet laudantium\nomnis quibusdam delectus saepe quia accusamus maiores nam est\ncum et ducimus et vero voluptates excepturi deleniti ratione",
            ],
        ])->as('thirds'),
    ])->run();
    expect($responses)->toBeArray()->toHaveCount(3);
});

it('does not run concurrent requests twice', function () {
    $http = app(\Illuminate\Http\Client\Factory::class);
    $http->fake();

    $requests = [
        TestRequest::build(http: $http),
        TestRequest::build(http: $http),
    ];

    Concurrently::build(http: $http)->setRequests($requests)->run();

    $http->assertSentCount(2);
});

it('can actually run concurrency', function () {
    $requests = [
        TestRequest::fake()->withQuery(
            query: [
                'postId' => 1,
            ],
        )->setPath(
            path: '/comments',
        )->withFakeData([
            [
                'postId' => 1,
                'id' => 1,
                'name' => 'id labore ex et quam laborum',
                'email' => 'Eliseo@gardner.biz',
                'body' => "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium",
            ],
            [
                'postId' => 1,
                'id' => 2,
                'name' => 'quo vero reiciendis velit similique earum',
                'email' => 'Jayne_Kuhic@sydney.com',
                'body' => "est natus enim nihil est dolore omnis voluptatem numquam\net omnis occaecati quod ullam at\nvoluptatem error expedita pariatur\nnihil sint nostrum voluptatem reiciendis et",
            ],
            [
                'postId' => 1,
                'id' => 3,
                'name' => 'odio adipisci rerum aut animi',
                'email' => 'Nikita@garfield.biz',
                'body' => "quia molestiae reprehenderit quasi aspernatur\naut expedita occaecati aliquam eveniet laudantium\nomnis quibusdam delectus saepe quia accusamus maiores nam est\ncum et ducimus et vero voluptates excepturi deleniti ratione",
            ],
        ])->as('first'),
        TestRequest::fake()->withQuery(
            query: [
                'postId' => 2,
            ],
        )->setPath(
            path: '/comments',
        )->withFakeData([
            [
                'postId' => 2,
                'id' => 4,
                'name' => 'id labore ex et quam laborum',
                'email' => 'Eliseo@gardner.biz',
                'body' => "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium",
            ],
            [
                'postId' => 2,
                'id' => 5,
                'name' => 'quo vero reiciendis velit similique earum',
                'email' => 'Jayne_Kuhic@sydney.com',
                'body' => "est natus enim nihil est dolore omnis voluptatem numquam\net omnis occaecati quod ullam at\nvoluptatem error expedita pariatur\nnihil sint nostrum voluptatem reiciendis et",
            ],
            [
                'postId' => 2,
                'id' => 6,
                'name' => 'odio adipisci rerum aut animi',
                'email' => 'Nikita@garfield.biz',
                'body' => "quia molestiae reprehenderit quasi aspernatur\naut expedita occaecati aliquam eveniet laudantium\nomnis quibusdam delectus saepe quia accusamus maiores nam est\ncum et ducimus et vero voluptates excepturi deleniti ratione",
            ],
        ])->as('second'),
        TestRequest::fake()->withQuery(
            query: [
                'postId' => 3,
            ],
        )->setPath(
            path: '/comments',
        )->withFakeData([
            [
                'postId' => 3,
                'id' => 7,
                'name' => 'id labore ex et quam laborum',
                'email' => 'Eliseo@gardner.biz',
                'body' => "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium",
            ],
            [
                'postId' => 3,
                'id' => 8,
                'name' => 'quo vero reiciendis velit similique earum',
                'email' => 'Jayne_Kuhic@sydney.com',
                'body' => "est natus enim nihil est dolore omnis voluptatem numquam\net omnis occaecati quod ullam at\nvoluptatem error expedita pariatur\nnihil sint nostrum voluptatem reiciendis et",
            ],
            [
                'postId' => 3,
                'id' => 9,
                'name' => 'odio adipisci rerum aut animi',
                'email' => 'Nikita@garfield.biz',
                'body' => "quia molestiae reprehenderit quasi aspernatur\naut expedita occaecati aliquam eveniet laudantium\nomnis quibusdam delectus saepe quia accusamus maiores nam est\ncum et ducimus et vero voluptates excepturi deleniti ratione",
            ],
        ])->as('third'),
    ];
    foreach ($requests as $request) {
        expect($request->send()->json())->toBeArray()->toHaveCount(3);
    }
    $responses = Concurrently::fake()->setRequests($requests)->run();
    foreach ($responses as $response) {
        expect($response->json())->toBeArray()->toHaveCount(3);
    }
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
            'postId' => 1,
            'id' => 1,
            'name' => 'id labore ex et quam laborum',
            'email' => 'Eliseo@gardner.biz',
            'body' => "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium",
        ],
        [
            'postId' => 1,
            'id' => 2,
            'name' => 'quo vero reiciendis velit similique earum',
            'email' => 'Jayne_Kuhic@sydney.com',
            'body' => "est natus enim nihil est dolore omnis voluptatem numquam\net omnis occaecati quod ullam at\nvoluptatem error expedita pariatur\nnihil sint nostrum voluptatem reiciendis et",
        ],
        [
            'postId' => 1,
            'id' => 3,
            'name' => 'odio adipisci rerum aut animi',
            'email' => 'Nikita@garfield.biz',
            'body' => "quia molestiae reprehenderit quasi aspernatur\naut expedita occaecati aliquam eveniet laudantium\nomnis quibusdam delectus saepe quia accusamus maiores nam est\ncum et ducimus et vero voluptates excepturi deleniti ratione",
        ],
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
            filename: __DIR__.'/../../stubs/api-request.stub',
        )
    )->toBeTrue();

    Artisan::call(
        command: TransporterCommand::class,
        parameters: ['name' => 'TestRequest'],
    );

    expect(
        file_exists(
            filename: __DIR__.'/../../vendor/orchestra/testbench-core/laravel/app/Transporter/Requests/TestRequest.php'
        )
    )->toBeTrue();
});

it('can create a fake response', function () {
    expect(
        PostRequest::fake()->send()->json('userId')
    )->toEqual(100);
});

it('can append a string to the path', function () {
    $request = PostRequest::fake();

    expect(
        $request->path(),
    )->toEqual('/posts');

    $request->appendPath(
        appends: '1234',
    );

    expect(
        $request->path(),
    )->toEqual('/posts/1234');
});

it('can set a base uri using env and config', function () {
    expect(
        PostRequest::fake()->getBaseUrl(),
    )->toEqual('https://jsonplaceholder.typicode.com');

    config([
        'transporter' => [
            'base_uri' => 'https://example.com',
        ],
    ]);

    expect(
        BaseUriRequest::fake()->getBaseUrl()
    )->toEqual('https://example.com');
});

it('can set a base uri using lockOn alias', function () {
    expect(
        PostRequest::fake()->getBaseUrl(),
    )->toEqual('https://jsonplaceholder.typicode.com');

    $request = PostRequest::fake()->lockOn(
        baseUrl: 'https://example.com'
    );

    expect(
       $request->getBaseUrl()
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

it('can get the request payload', function () {
    $data = [
        'pest' => 'test',
    ];

    expect(
        PostRequest::build()->withData(
            data: $data,
        )->payload(),
    )->toEqual(
        expected: $data,
    );
});

it('applies pending request calls', function () {
    $http = app(\Illuminate\Http\Client\Factory::class);
    $http->fake();

    TestRequest::build(http: $http)
        ->withHeaders(['X-Test' => 'test'])
        ->send();

    $http->assertSent(function (\Illuminate\Http\Client\Request $request) {
        return $request->hasHeader('X-Test', 'test');
    });
});

it('applies withRequest and pending request calls concurrently', function () {
    $http = app(\Illuminate\Http\Client\Factory::class);
    $http->fake();

    $requests = [
        TestRequest::build(http: $http)
            ->as('first')
            ->withHeaders(['X-Test' => '1']),
        TestRequest::build(http: $http)
            ->as('second')
            ->withHeaders(['X-Test' => '2']),
    ];

    expect(
        Concurrently::build(http: $http)->setRequests($requests)->run()
    )->toHaveKeys(['first', 'second']);

    $http->assertSent(function (\Illuminate\Http\Client\Request $request) {
        return
            $request->hasHeader('Authorization', 'Bearer foobar') &&
            $request->hasHeader('X-Test', '1');
    });

    $http->assertSent(function (\Illuminate\Http\Client\Request $request) {
        return
            $request->hasHeader('Authorization', 'Bearer foobar') &&
            $request->hasHeader('X-Test', '2');
    });
});
