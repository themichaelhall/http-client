<?php

/**
 * This file is a part of the http-client package.
 *
 * Read more at https://github.com/themichaelhall/http-client
 */

declare(strict_types=1);

namespace MichaelHall\HttpClient;

/**
 * HTTP client response class.
 *
 * @since 1.0.0
 */
class HttpClientResponse implements HttpClientResponseInterface
{
    /**
     * Constructs a HTTP client response.
     *
     * @since 1.0.0
     *
     * @param int    $httpCode The http code.
     * @param string $content  The content.
     */
    public function __construct(int $httpCode = 200, string $content = '')
    {
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->headers = [];
    }

    /**
     * Adds a header.
     *
     * @since 1.0.0
     *
     * @param string $header The header.
     */
    public function addHeader(string $header): void
    {
        $this->headers[] = $header;
    }

    /**
     * Returns the content.
     *
     * @since 1.0.0
     *
     * @return string The content.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Returns the headers.
     *
     * @since 1.0.0
     *
     * @return string[] The headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Returns the HTTP code.
     *
     * @since 1.0.0
     *
     * @return int The HTTP code.
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     * Returns true if the response is successful, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if the response is successful, false otherwise.
     */
    public function isSuccessful(): bool
    {
        return $this->httpCode >= 200 && $this->httpCode < 300;
    }

    /**
     * @var int The http code.
     */
    private int $httpCode;

    /**
     * @var string The content.
     */
    private string $content;

    /**
     * @var string[] The headers.
     */
    private array $headers;
}
