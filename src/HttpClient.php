<?php

/**
 * This file is a part of the http-client package.
 *
 * Read more at https://github.com/themichaelhall/http-client
 */

declare(strict_types=1);

namespace MichaelHall\HttpClient;

use MichaelHall\HttpClient\RequestHandlers\CurlRequestHandler;
use MichaelHall\HttpClient\RequestHandlers\RequestHandlerInterface;

/**
 * HTTP client class.
 *
 * @since 1.0.0
 */
class HttpClient implements HttpClientInterface
{
    /**
     * Constructs the HTTP client.
     *
     * @since 1.0.0
     *
     * @param RequestHandlerInterface|null $requestHandler The optional request handler.
     */
    public function __construct(?RequestHandlerInterface $requestHandler = null)
    {
        $this->requestHandler = $requestHandler ?? new CurlRequestHandler();
    }

    /**
     * Sends a request.
     *
     * @since 1.0.0
     *
     * @param HttpClientRequestInterface $request The request.
     *
     * @return HttpClientResponseInterface The response.
     */
    public function send(HttpClientRequestInterface $request): HttpClientResponseInterface
    {
        return $this->requestHandler->handleRequest($request);
    }

    /**
     * @var RequestHandlerInterface My request handler.
     */
    private $requestHandler;
}
