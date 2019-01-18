<?php

declare(strict_types=1);

namespace MichaelHall\HttpClient\Tests;

use DataTypes\FilePath;
use DataTypes\Url;
use MichaelHall\HttpClient\HttpClient;
use MichaelHall\HttpClient\HttpClientRequest;
use PHPUnit\Framework\TestCase;

/**
 * Test HttpClient class.
 */
class HttpClientTest extends TestCase
{
    /**
     * Test fetching a page with 200 OK response.
     */
    public function testOkResponse()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://httpbin.org/anything'));
        $request->addHeader('X-Test-Header: Foo Bar');
        $response = $client->send($request);
        $jsonContent = json_decode($response->getContent(), true);

        self::assertSame(200, $response->getHttpCode());
        self::assertSame('GET', $jsonContent['method']);
        self::assertSame('Foo Bar', $jsonContent['headers']['X-Test-Header']);
        self::assertTrue($response->isSuccessful());
    }

    /**
     * Test fetching a page with failed connection.
     */
    public function testFailedConnectionResponse()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://localhost:123/'));
        $response = $client->send($request);

        self::assertSame(0, $response->getHttpCode());
        self::assertSame('Failed to connect to localhost port 123: Connection refused', $response->getContent());
        self::assertFalse($response->isSuccessful());
    }

    /**
     * Test fetching a page with 404 Not Found response.
     */
    public function testNotFoundResponse()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://httpbin.org/status/404'));
        $response = $client->send($request);

        self::assertSame(404, $response->getHttpCode());
        self::assertSame('', $response->getContent());
        self::assertFalse($response->isSuccessful());
    }

    /**
     * Test a POST request.
     */
    public function testPostRequest()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://httpbin.org/anything'), 'POST');
        $request->addHeader('Expect: 100-continue');
        $request->setPostField('Foo', 'Bar');
        $response = $client->send($request);
        $jsonContent = json_decode($response->getContent(), true);

        self::assertSame(200, $response->getHttpCode());
        self::assertSame('POST', $jsonContent['method']);
        self::assertSame(['Foo' => 'Bar'], $jsonContent['form']);
        self::assertSame('application/x-www-form-urlencoded', $jsonContent['headers']['Content-Type']);
        self::assertTrue($response->isSuccessful());
    }

    /**
     * Test a POST request with files.
     */
    public function testPostRequestWithFiles()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://httpbin.org/anything'), 'POST');
        $request->setFile('Foo', FilePath::parse(__DIR__ . '/TestFiles/hello-world.txt'));
        $request->setPostField('Bar', 'Baz');
        $response = $client->send($request);
        $jsonContent = json_decode($response->getContent(), true);

        self::assertSame(200, $response->getHttpCode());
        self::assertSame('POST', $jsonContent['method']);
        self::assertSame(['Foo' => 'Hello World!'], $jsonContent['files']);
        self::assertSame(['Bar' => 'Baz'], $jsonContent['form']);
        self::assertStringStartsWith('multipart/form-data', $jsonContent['headers']['Content-Type']);
        self::assertTrue($response->isSuccessful());
    }

    /**
     * Test fetching a page with custom headers.
     */
    public function testWithCustomHeaders()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://httpbin.org/response-headers?X-Test-Header=Foo'));
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertContains('X-Test-Header: Foo', $response->getHeaders());
        self::assertTrue($response->isSuccessful());
    }

    /**
     * Test a PUT request with raw content.
     */
    public function testWithRawContent()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://httpbin.org/anything'), 'PUT');
        $request->addHeader('Content-Type: application/json');
        $request->setRawContent('{"Foo": "Bar"}');
        $response = $client->send($request);
        $jsonContent = json_decode($response->getContent(), true);

        self::assertSame(200, $response->getHttpCode());
        self::assertSame('PUT', $jsonContent['method']);
        self::assertSame(['Foo' => 'Bar'], $jsonContent['json']);
        self::assertSame('application/json', $jsonContent['headers']['Content-Type']);
        self::assertTrue($response->isSuccessful());
    }

    /**
     * Test fetching pages with cookies.
     */
    public function testWithCookies()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://httpbin.org/cookies/set?Foo=Bar'));
        $client->send($request);
        $request = new HttpClientRequest(Url::parse('https://httpbin.org/cookies'));
        $response = $client->send($request);
        $jsonContent = json_decode($response->getContent(), true);

        self::assertSame(200, $response->getHttpCode());
        self::assertSame(['Foo' => 'Bar'], $jsonContent['cookies']);
        self::assertTrue($response->isSuccessful());
    }
}
