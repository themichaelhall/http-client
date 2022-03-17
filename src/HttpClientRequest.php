<?php

/**
 * This file is a part of the http-client package.
 *
 * Read more at https://github.com/themichaelhall/http-client
 */

declare(strict_types=1);

namespace MichaelHall\HttpClient;

use DataTypes\Net\UrlInterface;
use DataTypes\System\FilePathInterface;

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
     *
     * @return $this
     */
    public function addHeader(string $header): self
    {
        $this->headers[] = $header;

        return $this;
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
     *
     * @return $this
     */
    public function setCACertificate(FilePathInterface $caCertificate): self
    {
        $this->caCertificate = $caCertificate;

        return $this;
    }

    /**
     * Sets the client certificate path.
     *
     * @since 1.1.0
     *
     * @param FilePathInterface $clientCertificate The client certificate path.
     *
     * @return $this
     */
    public function setClientCertificate(FilePathInterface $clientCertificate): self
    {
        $this->clientCertificate = $clientCertificate;

        return $this;
    }

    /**
     * Sets the client certificate password.
     *
     * @since 1.3.0
     *
     * @param string $clientCertificatePassword The client certificate password.
     *
     * @return $this
     */
    public function setClientCertificatePassword(string $clientCertificatePassword): self
    {
        $this->clientCertificatePassword = $clientCertificatePassword;

        return $this;
    }

    /**
     * Sets the client certificate type.
     *
     * @since 1.3.0
     *
     * @param string $clientCertificateType
     *
     * return $this
     */
    public function setClientCertificateType(string $clientCertificateType): self
    {
        $this->clientCertificateType = $clientCertificateType;

        return $this;
    }

    /**
     * Sets the client key path.
     *
     * @since 1.1.0
     *
     * @param FilePathInterface $clientKey The client key path.
     *
     * @return $this
     */
    public function setClientKey(FilePathInterface $clientKey): self
    {
        $this->clientKey = $clientKey;

        return $this;
    }

    /**
     * Sets a file to upload.
     *
     * @since 1.0.0
     *
     * @param string            $name     The name.
     * @param FilePathInterface $filePath The file path.
     *
     * @return $this
     */
    public function setFile(string $name, FilePathInterface $filePath): self
    {
        $this->rawContent = '';
        $this->files[$name] = $filePath;

        return $this;
    }

    /**
     * Sets a post field.
     *
     * @since 1.0.0
     *
     * @param string $name  The name.
     * @param string $value The value.
     *
     * @return $this
     */
    public function setPostField(string $name, string $value): self
    {
        $this->rawContent = '';
        $this->postFields[$name] = $value;

        return $this;
    }

    /**
     * Sets the raw content.
     *
     * @since 1.0.0
     *
     * @param string $rawContent The raw content.
     *
     * @return $this
     */
    public function setRawContent(string $rawContent): self
    {
        $this->postFields = [];
        $this->files = [];
        $this->rawContent = $rawContent;

        return $this;
    }

    /**
     * @var UrlInterface The url.
     */
    private UrlInterface $url;

    /**
     * @var string The method.
     */
    private string $method;

    /**
     * @var string[] The headers.
     */
    private array $headers;

    /**
     * @var array The post fields.
     */
    private array $postFields;

    /**
     * @var array The files.
     */
    private array $files;

    /**
     * @var string The raw content.
     */
    private string $rawContent;

    /**
     * @var FilePathInterface|null The CA certificate path.
     */
    private ?FilePathInterface $caCertificate;

    /**
     * @var FilePathInterface|null The client certificate path.
     */
    private ?FilePathInterface $clientCertificate;

    /**
     * @var string|null The client certificate password.
     */
    private ?string $clientCertificatePassword;

    /**
     * @var string|null The client certificate type.
     */
    private ?string $clientCertificateType;

    /**
     * @var FilePathInterface|null The client key.
     */
    private ?FilePathInterface $clientKey;
}
