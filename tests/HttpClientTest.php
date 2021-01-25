<?php

declare(strict_types=1);

namespace MichaelHall\HttpClient\Tests;

use CURLFile;
use DataTypes\FilePath;
use DataTypes\Url;
use MichaelHall\HttpClient\HttpClient;
use MichaelHall\HttpClient\HttpClientRequest;
use MichaelHall\HttpClient\RequestHandlers\CurlRequestHandler;
use MichaelHall\HttpClient\Tests\Helpers\Fakes\FakeCurl;
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
        $request = new HttpClientRequest(Url::parse('https://example.com/'));
        $request->addHeader('X-Test-Header: Foo Bar');
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertTrue($response->isSuccessful());
        self::assertSame([], $response->getHeaders());
        self::assertSame('Hello World!', $response->getContent());

        self::assertSame('https://example.com/', FakeCurl::getOption(CURLOPT_URL));
        self::assertSame('GET', FakeCurl::getOption(CURLOPT_CUSTOMREQUEST));
        self::assertSame(['X-Test-Header: Foo Bar'], FakeCurl::getOption(CURLOPT_HTTPHEADER));
        self::assertNull(FakeCurl::getOption(CURLOPT_POSTFIELDS));
    }

    /**
     * Test fetching a page with 100 Continue response.
     */
    public function testContinueResponse()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://example.com/continue'));
        $request->addHeader('X-Test-Header: Foo Bar');
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertTrue($response->isSuccessful());
        self::assertSame([], $response->getHeaders());
        self::assertSame('Hello World!', $response->getContent());

        self::assertSame('https://example.com/continue', FakeCurl::getOption(CURLOPT_URL));
        self::assertSame('GET', FakeCurl::getOption(CURLOPT_CUSTOMREQUEST));
        self::assertSame(['X-Test-Header: Foo Bar'], FakeCurl::getOption(CURLOPT_HTTPHEADER));
        self::assertNull(FakeCurl::getOption(CURLOPT_POSTFIELDS));
    }

    /**
     * Test fetching a page with failed connection.
     */
    public function testFailedConnectionResponse()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://localhost/'));
        $response = $client->send($request);

        self::assertSame(0, $response->getHttpCode());
        self::assertFalse($response->isSuccessful());
        self::assertSame([], $response->getHeaders());
        self::assertSame('Failed to connect to localhost: Connection refused', $response->getContent());

        self::assertSame('https://localhost/', FakeCurl::getOption(CURLOPT_URL));
        self::assertSame('GET', FakeCurl::getOption(CURLOPT_CUSTOMREQUEST));
        self::assertSame([], FakeCurl::getOption(CURLOPT_HTTPHEADER));
        self::assertNull(FakeCurl::getOption(CURLOPT_POSTFIELDS));
    }

    /**
     * Test fetching a page with 404 Not Found response.
     */
    public function testNotFoundResponse()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://example.com/notfound'));
        $response = $client->send($request);

        self::assertSame(404, $response->getHttpCode());
        self::assertFalse($response->isSuccessful());
        self::assertSame([], $response->getHeaders());
        self::assertSame('Not found', $response->getContent());

        self::assertSame('https://example.com/notfound', FakeCurl::getOption(CURLOPT_URL));
        self::assertSame('GET', FakeCurl::getOption(CURLOPT_CUSTOMREQUEST));
        self::assertSame([], FakeCurl::getOption(CURLOPT_HTTPHEADER));
        self::assertNull(FakeCurl::getOption(CURLOPT_POSTFIELDS));
    }

    /**
     * Test a POST request.
     */
    public function testPostRequest()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://example.com/'), 'POST');
        $request->setPostField('Foo', 'Bar');
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertTrue($response->isSuccessful());
        self::assertSame([], $response->getHeaders());
        self::assertSame('Hello World!', $response->getContent());

        self::assertSame('https://example.com/', FakeCurl::getOption(CURLOPT_URL));
        self::assertSame('POST', FakeCurl::getOption(CURLOPT_CUSTOMREQUEST));
        self::assertSame([], FakeCurl::getOption(CURLOPT_HTTPHEADER));
        self::assertSame('Foo=Bar', FakeCurl::getOption(CURLOPT_POSTFIELDS));
    }

    /**
     * Test a POST request with files.
     */
    public function testPostRequestWithFiles()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://example.com/'), 'POST');
        $request->setFile('Foo', FilePath::parse(__DIR__ . '/TestFiles/hello-world.txt'));
        $request->setPostField('Bar', 'Baz');
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertTrue($response->isSuccessful());
        self::assertSame([], $response->getHeaders());
        self::assertSame('Hello World!', $response->getContent());

        self::assertSame('https://example.com/', FakeCurl::getOption(CURLOPT_URL));
        self::assertSame('POST', FakeCurl::getOption(CURLOPT_CUSTOMREQUEST));
        self::assertSame([], FakeCurl::getOption(CURLOPT_HTTPHEADER));

        $postFields = FakeCurl::getOption(CURLOPT_POSTFIELDS);

        self::assertCount(2, $postFields);
        self::assertInstanceOf(CURLFile::class, $postFields['Foo']);
        self::assertSame(FilePath::parse(__DIR__ . '/TestFiles/hello-world.txt')->__toString(), $postFields['Foo']->getFilename());
        self::assertSame('text/plain', $postFields['Foo']->getMimeType());
        self::assertSame('hello-world.txt', $postFields['Foo']->getPostFilename());
    }

    /**
     * Test fetching a page with response header.
     */
    public function testWithResponseHeader()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://example.com/response-header?X-Test-Header%3A+Foo'));
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertTrue($response->isSuccessful());
        self::assertSame(['X-Test-Header: Foo'], $response->getHeaders());
        self::assertSame('Hello World!', $response->getContent());

        self::assertSame('https://example.com/response-header?X-Test-Header%3A+Foo', FakeCurl::getOption(CURLOPT_URL));
        self::assertSame('GET', FakeCurl::getOption(CURLOPT_CUSTOMREQUEST));
        self::assertSame([], FakeCurl::getOption(CURLOPT_HTTPHEADER));
        self::assertNull(FakeCurl::getOption(CURLOPT_POSTFIELDS));
    }

    /**
     * Test a PUT request with raw content.
     */
    public function testWithRawContent()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://example.com/'), 'PUT');
        $request->addHeader('Content-Type: application/json');
        $request->setRawContent('{"Foo": "Bar"}');
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertTrue($response->isSuccessful());
        self::assertSame([], $response->getHeaders());
        self::assertSame('Hello World!', $response->getContent());

        self::assertSame('https://example.com/', FakeCurl::getOption(CURLOPT_URL));
        self::assertSame('PUT', FakeCurl::getOption(CURLOPT_CUSTOMREQUEST));
        self::assertSame(['Content-Type: application/json'], FakeCurl::getOption(CURLOPT_HTTPHEADER));
        self::assertSame('{"Foo": "Bar"}', FakeCurl::getOption(CURLOPT_POSTFIELDS));
    }

    /**
     * Test fetching a page using client certificates.
     */
    public function testWithClientCertificates()
    {
        $client = new HttpClient();
        $request = new HttpClientRequest(Url::parse('https://example.com/'));
        $request->setCACertificate(FilePath::parse(__DIR__ . '/TestFiles/cacert.pem'));
        $request->setClientCertificate(FilePath::parse(__DIR__ . '/TestFiles/cert.pem'));
        $request->setClientCertificatePassword('FooBar');
        $request->setClientCertificateType('PEM');
        $request->setClientKey(FilePath::parse(__DIR__ . '/TestFiles/key.pem'));
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertTrue($response->isSuccessful());
        self::assertSame([], $response->getHeaders());
        self::assertSame('Hello World!', $response->getContent());

        self::assertSame('https://example.com/', FakeCurl::getOption(CURLOPT_URL));
        self::assertSame('GET', FakeCurl::getOption(CURLOPT_CUSTOMREQUEST));
        self::assertSame([], FakeCurl::getOption(CURLOPT_HTTPHEADER));
        self::assertNull(FakeCurl::getOption(CURLOPT_POSTFIELDS));

        self::assertSame(FilePath::parse(__DIR__ . '/TestFiles/cacert.pem')->__toString(), FakeCurl::getOption(CURLOPT_CAINFO));
        self::assertSame(FilePath::parse(__DIR__ . '/TestFiles/cert.pem')->__toString(), FakeCurl::getOption(CURLOPT_SSLCERT));
        self::assertSame('FooBar', FakeCurl::getOption(CURLOPT_SSLCERTPASSWD));
        self::assertSame('PEM', FakeCurl::getOption(CURLOPT_SSLCERTTYPE));
        self::assertSame(FilePath::parse(__DIR__ . '/TestFiles/key.pem')->__toString(), FakeCurl::getOption(CURLOPT_SSLKEY));
    }

    /**
     * Test fetching a page with overridden curl options.
     */
    public function testWithOverriddenCurlOptions()
    {
        $curlRequestHandler = new CurlRequestHandler();
        $curlRequestHandler->setOption(CURLOPT_TIMEOUT, 123);

        $client = new HttpClient($curlRequestHandler);
        $request = new HttpClientRequest(Url::parse('https://example.com/'));
        $request->addHeader('X-Test-Header: Foo Bar');
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertTrue($response->isSuccessful());
        self::assertSame([], $response->getHeaders());
        self::assertSame('Hello World!', $response->getContent());

        self::assertSame('https://example.com/', FakeCurl::getOption(CURLOPT_URL));
        self::assertSame('GET', FakeCurl::getOption(CURLOPT_CUSTOMREQUEST));
        self::assertSame(['X-Test-Header: Foo Bar'], FakeCurl::getOption(CURLOPT_HTTPHEADER));
        self::assertNull(FakeCurl::getOption(CURLOPT_POSTFIELDS));
        self::assertSame(123, FakeCurl::getOption(CURLOPT_TIMEOUT));
    }

    /**
     * Set up.
     */
    protected function setUp(): void
    {
        parent::setUp();

        FakeCurl::enable();
    }

    /**
     * Tear down.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        FakeCurl::disable();
    }
}
