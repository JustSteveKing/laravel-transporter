<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Tests;

use Illuminate\Http\Client\PendingRequest;
use JustSteveKing\Transporter\Tests\Stubs\TestRequest;
use JustSteveKing\Transporter\Tests\TestCase;

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
            actual: TestRequest::build()->getRequest(),
        );
    }

    /**
     * @test
     */
    public function it_can_send_a_request()
    {
        $response = TestRequest::build()->setPath(
            path: '/todos/1',
        )->send();

        $this->assertFalse(
            condition: empty($response->json())
        );

        $this->assertJson(
            actualJson: json_encode([
                'userId' => 1,
                'id' => 1,
                'title' => 'delectus aut autem',
                'completed' => false
            ]),
        );
    }

    /**
     * @test
     */
    public function it_can_add_query_params()
    {
        $response = TestRequest::build()->setPath(
            path: '/comments',
        )->withQuery(
            query: [
                'postId' => 1,
            ],
        )->send();

        $this->assertFalse(
            condition: empty($response->json()),
        );

        $this->assertCount(
            expectedCount: 5,
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
}
