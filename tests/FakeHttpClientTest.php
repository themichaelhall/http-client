<?php

declare(strict_types=1);

namespace MichaelHall\HttpClient\Tests;

use DataTypes\FilePath;
use DataTypes\Url;
use MichaelHall\HttpClient\FakeHttpClient;
use MichaelHall\HttpClient\HttpClientRequest;
use MichaelHall\HttpClient\HttpClientRequestInterface;
use MichaelHall\HttpClient\HttpClientResponse;
use MichaelHall\HttpClient\HttpClientResponseInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test FakeHttpClient class.
 */
class FakeHttpClientTest extends TestCase
{
    /**
     * Test fetch a page returning the default response.
     */
    public function testDefaultResponse()
    {
        $request = new HttpClientRequest(Url::parse('https://example.org/'));
        $client = new FakeHttpClient();
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertSame('', $response->getContent());
        self::assertSame([], $response->getHeaders());
    }

    /**
     * Test a basic request.
     */
    public function testBasicRequest()
    {
        $request = new HttpClientRequest(Url::parse('https://example.org/'));
        $client = new FakeHttpClient();
        $client->setResponseHandler($this->responseHandler);
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertSame("Method=[GET]\nUrl=[https://example.org/]\nHeaders=[]", $response->getContent());
        self::assertSame(['X-Request-Url: https://example.org/'], $response->getHeaders());
    }

    /**
     * Test a not found request.
     */
    public function testNotFoundRequest()
    {
        $request = new HttpClientRequest(Url::parse('https://example.org/notfound'));
        $client = new FakeHttpClient();
        $client->setResponseHandler($this->responseHandler);
        $response = $client->send($request);

        self::assertSame(404, $response->getHttpCode());
        self::assertSame("Method=[GET]\nUrl=[https://example.org/notfound]\nHeaders=[]", $response->getContent());
        self::assertSame(['X-Request-Url: https://example.org/notfound'], $response->getHeaders());
    }

    /**
     * Test a POST request.
     */
    public function testPostRequest()
    {
        $request = new HttpClientRequest(Url::parse('https://example.org/'), 'POST');
        $client = new FakeHttpClient();
        $client->setResponseHandler($this->responseHandler);
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertSame("Method=[POST]\nUrl=[https://example.org/]\nHeaders=[]", $response->getContent());
        self::assertSame(['X-Request-Url: https://example.org/'], $response->getHeaders());
    }

    /**
     * Test a POST request with post fields.
     */
    public function testPostRequestWithPostFields()
    {
        $request = new HttpClientRequest(Url::parse('https://example.org/'), 'POST');
        $request->setPostField('Foo', 'Bar');
        $request->setPostField('Baz', '');
        $client = new FakeHttpClient();
        $client->setResponseHandler($this->responseHandler);
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertSame("Method=[POST]\nUrl=[https://example.org/]\nHeaders=[]\nPost[Foo]=[Bar]\nPost[Baz]=[]", $response->getContent());
        self::assertSame(['X-Request-Url: https://example.org/'], $response->getHeaders());
    }

    /**
     * Test a POST request with files.
     */
    public function testPostRequestWithFiles()
    {
        $request = new HttpClientRequest(Url::parse('https://example.org/'), 'POST');
        $filePath1 = FilePath::parse('/tmp/file1.txt');
        $filePath2 = FilePath::parse('/tmp/file2.txt');
        $request->setFile('Foo', $filePath1);
        $request->setFile('Baz', $filePath2);
        $client = new FakeHttpClient();
        $client->setResponseHandler($this->responseHandler);
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertSame("Method=[POST]\nUrl=[https://example.org/]\nHeaders=[]\nFile[Foo]=[$filePath1]\nFile[Baz]=[$filePath2]", $response->getContent());
        self::assertSame(['X-Request-Url: https://example.org/'], $response->getHeaders());
    }

    /**
     * Test a request with raw content.
     */
    public function testRequestWithRawContent()
    {
        $request = new HttpClientRequest(Url::parse('https://example.org/'), 'PUT');
        $request->addHeader('Content-Type: application/json');
        $request->setRawContent('{"Foo":"Bar"}');
        $client = new FakeHttpClient();
        $client->setResponseHandler($this->responseHandler);
        $response = $client->send($request);

        self::assertSame(200, $response->getHttpCode());
        self::assertSame("Method=[PUT]\nUrl=[https://example.org/]\nHeaders=[Content-Type: application/json]\nRawContent[{\"Foo\":\"Bar\"}]", $response->getContent());
        self::assertSame(['X-Request-Url: https://example.org/'], $response->getHeaders());
    }

    /**
     * Set up.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->responseHandler = function (HttpClientRequestInterface $request): HttpClientResponseInterface {
            return self::responseHandler($request);
        };
    }

    /**
     * My response handler.
     *
     * @param HttpClientRequestInterface $request The request.
     *
     * @return HttpClientResponseInterface The response.
     */
    private static function responseHandler(HttpClientRequestInterface $request): HttpClientResponseInterface
    {
        $httpCode = 200;

        if ($request->getUrl()->getPath()->__toString() === '/notfound') {
            $httpCode = 404;
        }

        $content = [
            'Method=[' . $request->getMethod() . ']',
            'Url=[' . $request->getUrl() . ']',
            'Headers=[' . implode('|', $request->getHeaders()) . ']',
        ];

        foreach ($request->getPostFields() as $name => $value) {
            $content[] = 'Post[' . $name . ']=[' . $value . ']';
        }

        foreach ($request->getFiles() as $name => $path) {
            $content[] = 'File[' . $name . ']=[' . $path . ']';
        }

        if ($request->getRawContent() !== '') {
            $content[] = 'RawContent[' . $request->getRawContent() . ']';
        }

        $response = new HttpClientResponse($httpCode, implode("\n", $content));
        $response->addHeader('X-Request-Url: ' . $request->getUrl());

        return $response;
    }

    /**
     * @var callable My response handler.
     */
    private $responseHandler;
}
