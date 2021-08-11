<?php

/**
 * This file is a part of the http-client package.
 *
 * Read more at https://github.com/themichaelhall/http-client
 */

declare(strict_types=1);

namespace MichaelHall\HttpClient\RequestHandlers;

use MichaelHall\HttpClient\HttpClientRequestInterface;
use MichaelHall\HttpClient\HttpClientResponseInterface;

/**
 * Request handler interface.
 *
 * @since 1.0.0
 */
interface RequestHandlerInterface
{
    /**
     * Handles a request.
     *
     * @since 1.0.0
     *
     * @param HttpClientRequestInterface $request The request.
     *
     * @return HttpClientResponseInterface The response.
     */
    public function handleRequest(HttpClientRequestInterface $request): HttpClientResponseInterface;
}
