<?php

declare(strict_types=1);

namespace MichaelHall\HttpClient\Tests;

use DataTypes\FilePath;
use DataTypes\Url;
use MichaelHall\HttpClient\HttpClientRequest;
use PHPUnit\Framework\TestCase;

/**
 * Test HttpClientRequest class.
 */
class HttpClientRequestTest extends TestCase
{
    /**
     * Tests a standard request.
     */
    public function testStandardRequest()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));

        self::assertSame('https://example.com/foo/bar', $request->getUrl()->__toString());
    }

    /**
     * Test getHeaders method.
     */
    public function testGetHeaders()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));

        self::assertSame([], $request->getHeaders());
    }

    /**
     * Test addHeader method.
     */
    public function testAddHeader()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));
        $request->addHeader('X-Test-Foo: Foo Header');
        $request->addHeader('X-Test-Bar: Bar Header');

        self::assertSame(['X-Test-Foo: Foo Header', 'X-Test-Bar: Bar Header'], $request->getHeaders());
    }

    /**
     * Test getMethod method.
     */
    public function testGetMethod()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'), 'PUT');

        self::assertSame('PUT', $request->getMethod());
    }

    /**
     * Test getPostFields method.
     */
    public function testGetPostFields()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));

        self::assertSame([], $request->getPostFields());
    }

    /**
     * Test setPostField method.
     */
    public function testSetPostField()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));
        $request->setPostField('Foo', 'Bar');
        $request->setPostField('Baz', '');

        self::assertSame(['Foo' => 'Bar', 'Baz' => ''], $request->getPostFields());
    }

    /**
     * Test getFiles method.
     */
    public function testGetFiles()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));

        self::assertSame([], $request->getFiles());
    }

    /**
     * Test setFile method.
     */
    public function testSetFile()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));
        $filePath1 = FilePath::parse('/tmp/file1');
        $filePath2 = FilePath::parse('/tmp/file1');
        $request->setFile('Foo', $filePath1);
        $request->setFile('Bar', $filePath2);

        self::assertSame(['Foo' => $filePath1, 'Bar' => $filePath2], $request->getFiles());
    }

    /**
     * Test getRawContent method.
     */
    public function testGetRawContent()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));

        self::assertSame('', $request->getRawContent());
    }

    /**
     * Test setRawContent method.
     */
    public function testSetRawContent()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));
        $request->setRawContent('Baz');

        self::assertSame('Baz', $request->getRawContent());
    }

    /**
     * Test getCACertificate method.
     */
    public function testGetCACertificate()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));

        self::assertNull($request->getCACertificate());
    }

    /**
     * Test setCACertificate method.
     */
    public function testSetCACertificate()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));
        $request->setCACertificate(FilePath::parse('/foo.pem'));

        self::assertSame(DIRECTORY_SEPARATOR . 'foo.pem', $request->getCACertificate()->__toString());
    }

    /**
     * Test getClientCertificate method.
     */
    public function testGetClientCertificate()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));

        self::assertNull($request->getClientCertificate());
    }

    /**
     * Test setClientCertificate method.
     */
    public function testSetClientCertificate()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));
        $request->setClientCertificate(FilePath::parse('/bar.pem'));

        self::assertSame(DIRECTORY_SEPARATOR . 'bar.pem', $request->getClientCertificate()->__toString());
    }

    /**
     * Test getClientCertificatePassword method.
     */
    public function testGetClientCertificatePassword()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));

        self::assertNull($request->getClientCertificatePassword());
    }

    /**
     * Test setClientCertificatePassword method.
     */
    public function testSetClientCertificatePassword()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));
        $request->setClientCertificatePassword('FooBar');

        self::assertSame('FooBar', $request->getClientCertificatePassword());
    }

    /**
     * Test getClientCertificateType method.
     */
    public function testGetClientCertificateType()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));

        self::assertNull($request->getClientCertificateType());
    }

    /**
     * Test setClientCertificateType method.
     */
    public function testSetClientCertificateType()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));
        $request->setClientCertificateType('P12');

        self::assertSame('P12', $request->getClientCertificateType());
    }

    /**
     * Test getClientKey method.
     */
    public function testGetClientKey()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));

        self::assertNull($request->getClientKey());
    }

    /**
     * Test setClientKey method.
     */
    public function testSetClientKey()
    {
        $request = new HttpClientRequest(Url::parse('https://example.com/foo/bar'));
        $request->setClientKey(FilePath::parse('/baz.pem'));

        self::assertSame(DIRECTORY_SEPARATOR . 'baz.pem', $request->getClientKey()->__toString());
    }
}
