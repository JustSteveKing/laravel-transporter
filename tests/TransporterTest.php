<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Tests;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Config;
use JustSteveKing\Transporter\Request;
use JustSteveKing\Transporter\Tests\Stubs\BaseUriRequest;
use JustSteveKing\Transporter\Tests\Stubs\PostRequest;
use JustSteveKing\Transporter\Tests\Stubs\TestRequest;
use JustSteveKing\Transporter\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class TransporterTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_creates_a_pending_request()
    {
        $this->assertInstanceOf(
            expected: PendingRequest::class,
            actual: TestRequest::fake()->getRequest(),
        );
    }

    /**
     * @test
     */
    public function it_can_send_a_request()
    {
        $response = TestRequest::fake()->setPath(
            path: '/todos/1',
        )->send();

        $this->assertEquals(
            expected: [],
            actual: $response->json(),
        );
    }

    /**
     * @test
     */
    public function it_can_add_query_params()
    {
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

        $this->assertFalse(
            condition: empty($response->json()),
        );

        $this->assertCount(
            expectedCount: 3,
            haystack: $response->json()
        );

        foreach ($response->json() as $item) {
            $this->assertEquals(
                expected: 1,
                actual: $item['postId'],
            );
        }
    }

    /**
     * @test
     */
    public function it_can_add_data_to_the_request()
    {
        $data = [
            'title' => 'transporter test',
            'body' => 'transporter test',
            'userId' => 1,
        ];

        $response = PostRequest::fake()->withData(
            data: $data
        )->send();

        $this->assertEquals(
            expected: Response::HTTP_OK,
            actual: $response->status()
        );
    }

    /**
     * @test
     */
    public function it_can_create_a_new_api_request_using_the_command()
    {
        $this->assertTrue(
            file_exists(
                __DIR__ . '/../stubs/api-request.stub'
            )
        );

        $this->artisan(
            command: 'make:api-request TestRequest',
        )->assertExitCode(
            exitCode: 0,
        );

        $this->assertTrue(
            file_exists(
                __DIR__ . '/../vendor/orchestra/testbench-core/laravel/app/Transporter/Requests/TestRequest.php'
            )
        );
    }

    /**
     * @test
     */
    public function it_can_create_a_fake_response()
    {
        $response = PostRequest::fake()->send();

        $this->assertEquals(
            expected: 100,
            actual:   $response->json("userId"),
        );
        $this->assertEquals(
            expected: 200,
            actual:   $response->status(),
        );
    }

    /**
     * @test
     */
    public function it_can_create_a_fake_response_with_status()
    {
        $response = PostRequest::fake()->withFakeStatus(404)->send();

        $this->assertEquals(
            expected: 404,
            actual:   $response->status(),
        );
    }

    /**
     * @test
     */
    public function it_can_set_a_base_uri_using_env_and_config()
    {
        $request = PostRequest::fake();

        $this->assertEquals(
            expected: 'https://jsonplaceholder.typicode.com',
            actual: $request->getBaseUrl(),
        );

        config([
            'transporter' => [
                'base_uri' => 'https://example.com'
            ]
        ]);

        $request = BaseUriRequest::fake();

        $this->assertEquals(
            expected: 'https://example.com',
            actual: $request->getBaseUrl(),
        );
    }
}
