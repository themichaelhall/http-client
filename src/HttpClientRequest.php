<?php

/**
 * This file is a part of the http-client package.
 *
 * Read more at https://github.com/themichaelhall/http-client
 */

declare(strict_types=1);

namespace MichaelHall\HttpClient;

use DataTypes\Interfaces\FilePathInterface;
use DataTypes\Interfaces\UrlInterface;

/**
 * HTTP client request class.
 *
 * @since 1.0.0
 */
class HttpClientRequest implements HttpClientRequestInterface
{
    /**
     * Constructs a HTTP client request.
     *
     * @since 1.0.0
     *
     * @param UrlInterface $url    The url.
     * @param string       $method The method.
     */
    public function __construct(UrlInterface $url, string $method = 'GET')
    {
        $this->url = $url;
        $this->method = $method;
        $this->headers = [];
        $this->postFields = [];
        $this->files = [];
        $this->rawContent = '';
        $this->caCertificate = null;
        $this->clientCertificate = null;
        $this->clientCertificatePassword = null;
        $this->clientCertificateType = null;
        $this->clientKey = null;
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
     * Returns the certificate authority (CA) certificate path or null if not set.
     *
     * @since 1.1.0
     *
     * @return FilePathInterface|null The certificate authority (CA) certificate path or null if not set.
     */
    public function getCACertificate(): ?FilePathInterface
    {
        return $this->caCertificate;
    }

    /**
     * Returns the client certificate path or null if not set.
     *
     * @since 1.1.0
     *
     * @return FilePathInterface|null The client certificate path or null if not set.
     */
    public function getClientCertificate(): ?FilePathInterface
    {
        return $this->clientCertificate;
    }

    /**
     * Returns the client certificate password or null if not set.
     *
     * @since 1.3.0
     *
     * @return string|null The client certificate password or null if not set.
     */
    public function getClientCertificatePassword(): ?string
    {
        return $this->clientCertificatePassword;
    }

    /**
     * Returns the client certificate type or null if not set.
     *
     * @since 1.3.0
     *
     * @return string|null
     */
    public function getClientCertificateType(): ?string
    {
        return $this->clientCertificateType;
    }

    /**
     * Returns the client key path or null if not set.
     *
     * @since 1.1.0
     *
     * @return FilePathInterface|null The client key path or null if not set.
     */
    public function getClientKey(): ?FilePathInterface
    {
        return $this->clientKey;
    }

    /**
     * Returns the files to upload.
     *
     * @since 1.0.0
     *
     * @return array The files to upload.
     */
    public function getFiles(): array
    {
        return $this->files;
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
     * Returns the method.
     *
     * @since 1.0.0
     *
     * @return string The method.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns the post fields.
     *
     * @since 1.0.0
     *
     * @return array The post fields.
     */
    public function getPostFields(): array
    {
        return $this->postFields;
    }

    /**
     * Returns the raw content.
     *
     * @since 1.0.0
     *
     * @return string The raw content.
     */
    public function getRawContent(): string
    {
        return $this->rawContent;
    }

    /**
     * Returns the url.
     *
     * @since 1.0.0
     *
     * @return UrlInterface The url.
     */
    public function getUrl(): UrlInterface
    {
        return $this->url;
    }

    /**
     * Sets the certificate authority (CA) certificate path.
     *
     * @since 1.1.0
     *
     * @param FilePathInterface $caCertificate The certificate authority (CA) certificate path.
     */
    public function setCACertificate(FilePathInterface $caCertificate): void
    {
        $this->caCertificate = $caCertificate;
    }

    /**
     * Sets the client certificate path.
     *
     * @since 1.1.0
     *
     * @param FilePathInterface $clientCertificate The client certificate path.
     */
    public function setClientCertificate(FilePathInterface $clientCertificate): void
    {
        $this->clientCertificate = $clientCertificate;
    }

    /**
     * Sets the client certificate password.
     *
     * @since 1.3.0
     *
     * @param string $clientCertificatePassword The client certificate password.
     */
    public function setClientCertificatePassword(string $clientCertificatePassword): void
    {
        $this->clientCertificatePassword = $clientCertificatePassword;
    }

    /**
     * Sets the client certificate type.
     *
     * @since 1.3.0
     *
     * @param string $clientCertificateType
     */
    public function setClientCertificateType(string $clientCertificateType): void
    {
        $this->clientCertificateType = $clientCertificateType;
    }

    /**
     * Sets the client key path.
     *
     * @since 1.1.0
     *
     * @param FilePathInterface $clientKey The client key path.
     */
    public function setClientKey(FilePathInterface $clientKey): void
    {
        $this->clientKey = $clientKey;
    }

    /**
     * Sets a file to upload.
     *
     * @since 1.0.0
     *
     * @param string            $name     The name.
     * @param FilePathInterface $filePath The file path.
     */
    public function setFile(string $name, FilePathInterface $filePath): void
    {
        $this->rawContent = '';
        $this->files[$name] = $filePath;
    }

    /**
     * Sets a post field.
     *
     * @since 1.0.0
     *
     * @param string $name  The name.
     * @param string $value The value.
     */
    public function setPostField(string $name, string $value): void
    {
        $this->rawContent = '';
        $this->postFields[$name] = $value;
    }

    /**
     * Sets the raw content.
     *
     * @since 1.0.0
     *
     * @param string $rawContent The raw content.
     */
    public function setRawContent(string $rawContent): void
    {
        $this->postFields = [];
        $this->files = [];
        $this->rawContent = $rawContent;
    }

    /**
     * @var UrlInterface My url.
     */
    private $url;

    /**
     * @var string My method.
     */
    private $method;

    /**
     * @var string[] My headers.
     */
    private $headers;

    /**
     * @var array My post fields.
     */
    private $postFields;

    /**
     * @var array My files.
     */
    private $files;

    /**
     * @var string My raw content.
     */
    private $rawContent;

    /**
     * @var FilePathInterface|null My CA certificate path.
     */
    private $caCertificate;

    /**
     * @var FilePathInterface|null My client certificate path.
     */
    private $clientCertificate;

    /**
     * @var string|null My client certificate password.
     */
    private $clientCertificatePassword;

    /**
     * @var string|null My client certificate type.
     */
    private $clientCertificateType;

    /**
     * @var FilePathInterface|null My client key.
     */
    private $clientKey;
}
