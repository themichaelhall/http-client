<?php
/**
 * This file is a part of the http-client package.
 *
 * Read more at https://github.com/themichaelhall/http-client
 */
declare(strict_types=1);

namespace MichaelHall\HttpClient;

/**
 * Fake HTTP client class.
 *
 * @since 1.0.0
 */
class FakeHttpClient implements HttpClientInterface
{
    /**
     * Constructs a fake HTTP client.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->responseHandler = null;
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
        if ($this->responseHandler === null) {
            return new HttpClientResponse();
        }

        return call_user_func($this->responseHandler, $request);
    }

    /**
     * Sets the response handler to use for returning a response.
     *
     * The handler must be a callable in form: function(HttpClientRequestInterface $request): HttpClientResponseInterface
     *
     * @since 1.0.0
     *
     * @param callable $responseHandler The response handler.
     */
    public function setResponseHandler(callable $responseHandler): void
    {
        $this->responseHandler = $responseHandler;
    }

    /**
     * @var callable|null My response handler.
     */
    private $responseHandler;
}
