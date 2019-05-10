<?php

declare(strict_types=1);

namespace MichaelHall\HttpClient\Tests\TestFiles;

use DataTypes\Interfaces\FilePathInterface;
use MichaelHall\HttpClient\HttpClientRequestInterface;
use MichaelHall\HttpClient\HttpClientResponse;
use MichaelHall\HttpClient\HttpClientResponseInterface;
use MichaelHall\HttpClient\RequestHandlers\RequestHandlerInterface;

/**
 * A custom request handler.
 *
 * A very rough emulation of some of the functionality from https://httpbin.org/.
 */
class CustomRequestHandler implements RequestHandlerInterface
{
    /**
     * CustomRequestHandler constructor.
     */
    public function __construct()
    {
        $this->cookies = [];
    }

    /**
     * Handles a request.
     *
     * @param HttpClientRequestInterface $request The request.
     *
     * @return HttpClientResponseInterface The response.
     */
    public function handleRequest(HttpClientRequestInterface $request): HttpClientResponseInterface
    {
        if ($request->getUrl()->getPort() !== 443) {
            return new HttpClientResponse(0, 'Failed to connect to ' . $request->getUrl()->getHost() . ' port ' . $request->getUrl()->getPort() . ': Connection refused');
        }

        $url = $request->getUrl();
        $path = $url->getPath();

        $responseCode = 200;
        $responseHeaders = [];
        $responseContent = [
            'method'  => $request->getMethod(),
            'headers' => [],
            'form'    => $request->getPostFields(),
            'files'   => [],
            'json'    => [],
            'cookies' => [],
        ];

        // Headers.
        foreach ($request->getHeaders() as $requestHeader) {
            $requestHeaderParts = explode(':', $requestHeader, 2);
            $responseContent['headers'][$requestHeaderParts[0]] = ltrim($requestHeaderParts[1]);
        }

        // Special paths.
        if ($path->getDirectory()->__toString() === '/status/') {
            $responseCode = intval($path->getFilename());
        } elseif ($path->__toString() === '/response-headers') {
            $queryStringParts = explode('=', $url->getQueryString());
            $responseHeaders[] = $queryStringParts[0] . ': ' . $queryStringParts[1];
        } elseif ($path->__toString() === '/cookies') {
            $responseContent['cookies'] = $this->cookies;
        } elseif ($path->__toString() === '/cookies/set') {
            $queryStringParts = explode('=', $url->getQueryString());
            $this->cookies[$queryStringParts[0]] = $queryStringParts[1];
        }

        // Files.
        foreach ($request->getFiles() as $name => $requestFile) {
            /** @var FilePathInterface $requestFile */
            $responseContent['files'][$name] = file_get_contents($requestFile->__toString());
        }

        // Raw content.
        if ($request->getRawContent() !== '') {
            $responseContent['json'] = json_decode($request->getRawContent(), true);
        }

        // Content-type.
        if (!empty($responseContent['files'])) {
            $responseContent['headers']['Content-Type'] = 'multipart/form-data';
        } elseif (!empty($responseContent['form'])) {
            $responseContent['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        // Other.
        if ($request->getCACertificate() !== null) {
            $responseContent['other']['CACertificate'] = $request->getCACertificate()->__toString();
        }

        if ($request->getClientCertificate() !== null) {
            $responseContent['other']['ClientCertificate'] = $request->getClientCertificate()->__toString();
        }

        if ($request->getClientKey() !== null) {
            $responseContent['other']['ClientKey'] = $request->getClientKey()->__toString();
        }

        // Create response.
        if ($responseCode === 404) {
            return new HttpClientResponse($responseCode);
        }

        $response = new HttpClientResponse($responseCode, json_encode($responseContent));
        foreach ($responseHeaders as $responseHeader) {
            $response->addHeader($responseHeader);
        }

        return $response;
    }

    /**
     * @var array My cookies.
     */
    private $cookies;
}
