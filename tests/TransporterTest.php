<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Tests;

use JustSteveKing\UriBuilder\Uri;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use JustSteveKing\Transporter\Transporter;
use Illuminate\Http\Client\RequestException;
use JustSteveKing\Transporter\Tests\TestCase;
use JustSteveKing\Transporter\Tests\Stubs\TestRequest;
use JustSteveKing\Transporter\Tests\Stubs\BasicRequest;
use JustSteveKing\Transporter\Tests\Stubs\TokenRequest;
use JustSteveKing\Transporter\Tests\Stubs\DigestRequest;
use JustSteveKing\Transporter\Tests\Stubs\FailingRequest;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class TransporterTest extends TestCase
{
    protected Transporter $transporter;

    public function setUp(): void
    {
        parent::setUp();

        $this->transporter = Transporter::request(
            request: new TestRequest(),
        );
    }

    /**
     * @test
     */
    public function it_can_build_a_transporter()
    {
        $this->assertInstanceOf(
            Transporter::class,
            $this->transporter,
        );
    }

    /**
     * @test
     */
    public function it_can_access_the_underlaying_request_that_has_been_passed()
    {
        $this->assertInstanceOf(
            TestRequest::class,
            $this->transporter->request,
        );
    }

    /**
     * @test
     */
    public function it_can_access_the_request_method_from_the_request()
    {
        $this->assertEquals(
            'GET',
            $this->transporter->request->method(),
        );
    }

    /**
     * @test
     */
    public function it_can_access_the_retry_times_from_the_request()
    {
        $this->assertEquals(
            3,
            $this->transporter->request->retry(),
        );
    }

    /**
     * @test
     */
    public function it_can_access_the_retry_timings_from_the_request()
    {
        $this->assertEquals(
            300.0,
            $this->transporter->request->retryTiming(),
        );
    }

    /**
     * @test
     */
    public function it_can_access_the_timeout_from_the_request()
    {
        $this->assertEquals(
            10.0,
            $this->transporter->request->timeout(),
        );
    }

    /**
     * @test
     */
    public function it_can_check_if_the_request_requires_authentication()
    {
        $this->assertFalse(
            $this->transporter->request->requiresAuth(),
        );
    }

    /**
     * @test
     */
    public function it_can_fetch_the_authentication_strategy_from_the_request()
    {
        $this->assertNull(
            $this->transporter->request->authStrategy(),
        );
    }

    /**
     * @test
     */
    public function it_can_fetch_the_authentication_credentials_from_the_request()
    {
        $this->assertNull(
            $this->transporter->request->authCredentials(),
        );
    }

    /**
     * @test
     */
    public function it_can_fetch_the_default_headers_for_a_request()
    {
        $this->assertNotEmpty(
            $this->transporter->request->headers(),
        );
    
        $this->assertTrue(
            array_key_exists('Accept', $this->transporter->request->headers()),
        );
    
        $this->assertEquals(
            [
                'Accept' => 'application/json',
            ],
            $this->transporter->request->headers()
        );
    }

    /**
     * @test
     */
    public function it_can_fetch_the_request_payload()
    {
        $this->assertTrue(
            is_array($this->transporter->request->payload()),
        );
    
        $this->assertTrue(
            empty($this->transporter->request->payload()),
        );
    }

    /**
     * @test
     */
    public function it_can_fetch_the_uri_from_the_request()
    {
        $this->assertInstanceOf(
            Uri::class,
            $this->transporter->request->uri(),
        );
    
        $this->assertEquals(
            'https',
            $this->transporter->request->uri()->scheme(),
        );
    
        $this->assertEquals(
            'jsonplaceholder.typicode.com',
            $this->transporter->request->uri()->host(),
        );
    
        $this->assertEquals(
            '/posts',
            $this->transporter->request->uri()->path(),
        );
    
        $this->assertEquals(
            'posts',
            $this->transporter->request->path(),
        );
    }

    /**
     * @test
     */
    public function it_can_get_the_query_parameters_for_the_request()
    {
        $this->assertTrue(
            is_array($this->transporter->request->parameters()),
        );
    
        $this->assertTrue(
            empty($this->transporter->request->parameters()),
        );
    }

    /**
     * @test
     */
    public function it_can_fetch_the_uri_fragment_from_the_request()
    {
        $this->assertNull(
            $this->transporter->request->fragment(),
        );
    }

    /**
     * @test
     */
    public function it_can_build_a_pending_request_to_send()
    {
        $request = $this->transporter->buildRequest();

        $this->assertInstanceOf(
            PendingRequest::class,
            $request
        );
    }

    /**
     * @test
     */
    public function it_can_send_requests()
    {
        $response = $this->transporter->send();

        $this->assertInstanceOf(
            Response::class,
            $response,
        );

        $this->assertTrue(
            ! empty($response->json())
        );
    }

    /**
     * @test
     */
    public function it_will_catch_any_failed_requests()
    {
        $this->expectException(
            RequestException::class,
        );

        $this->expectExceptionCode(
            HttpFoundationResponse::HTTP_NOT_FOUND,
        );

        $this->expectExceptionMessage(
            "HTTP request returned status code 404"
        );
        
        Transporter::request(
            request: new FailingRequest,
        )->send();
    }

    /**
     * @test
     */
    public function it_can_fake_a_request()
    {
        $this->transporter->fake([
            'https://jsonplaceholder.typicode.com/*' => Http::response(
                body: [
                    'foo' => 'bar',
                ]
            ),
        ]);

        $response = $this->transporter->send();

        $this->assertEquals(
            200,
            $response->status(),
        );
    }

    /**
     * @test
     */
    public function it_can_handle_token_based_auth()
    {
        $transporter = Transporter::request(
            request: new TokenRequest,
        );

        $options = $transporter->buildRequest()->mergeOptions();

        $this->assertTrue(
            array_key_exists(
                'Authorization',
                $options['headers']
            )
        );

        $this->assertTrue(
            str_starts_with(
                $options['headers']['Authorization'],
                'Bearer '
            )
        );
    }

    /**
     * @test
     */
    public function it_can_handle_digest_based_auth()
    {
        $transporter = Transporter::request(
            request: new DigestRequest,
        );
        
        $transporter->fake([
            'https://jsonplaceholder.typicode.com/*' => Http::response(
                body: [
                    'foo' => 'bar',
                ]
            ),
        ]);

        $options = $transporter->buildRequest()->mergeOptions();

        $this->assertTrue(
            array_key_exists(
                'auth',
                $options
            )
        );
    }

    /**
     * @test
     */
    public function it_can_handle_basic_based_auth()
    {
        $transporter = Transporter::request(
            request: new BasicRequest,
        );
        
        $transporter->fake([
            'https://jsonplaceholder.typicode.com/*' => Http::response(
                body: [
                    'foo' => 'bar',
                ]
            ),
        ]);

        $options = $transporter->buildRequest()->mergeOptions();

        [$username, $password] = $transporter->request->authCredentials();

        $this->assertEquals(
            $options['auth'][0],
            $username
        );

        $this->assertEquals(
            $options['auth'][1],
            $password
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
                __DIR__ . '/../vendor/orchestra/testbench-core/laravel/app/Http/API/Requests/TestRequest.php'
            )
        );
    }

    /**
     * @test
     */
    public function it_can_forward_calls_directly_from_the_request()
    {
        Http::fake([
            'https://jsonplaceholder.typicode.com/*' => Http::response(
                body: [
                    'foo' => 'bar',
                ]
            ),
        ]);

        $response = BasicRequest::with(
            payload: []
        )->send();

        $this->assertEquals(
            200,
            $response->status()
        );
    }
}
