<?php
/**
 * This file is a part of the http-client package.
 *
 * Read more at https://github.com/themichaelhall/http-client
 */
declare(strict_types=1);

namespace MichaelHall\HttpClient;

/**
 * HTTP client interface.
 *
 * @since 1.0.0
 */
interface HttpClientInterface
{
    /**
     * Sends a request.
     *
     * @since 1.0.0
     *
     * @param HttpClientRequestInterface $request The request.
     *
     * @return HttpClientResponseInterface The response.
     */
    public function send(HttpClientRequestInterface $request): HttpClientResponseInterface;
}
