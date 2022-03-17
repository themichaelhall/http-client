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
 * HTTP client request interface.
 *
 * @since 1.0.0
 */
interface HttpClientRequestInterface
{
    /**
     * Adds a header.
     *
     * @since 1.0.0
     *
     * @param string $header The header.
     *
     * @return $this
     */
    public function addHeader(string $header): self;

    /**
     * Returns the certificate authority (CA) certificate path or null if not set.
     *
     * @since 1.1.0
     *
     * @return FilePathInterface|null The certificate authority (CA) certificate path or null if not set.
     */
    public function getCACertificate(): ?FilePathInterface;

    /**
     * Returns the client certificate path or null if not set.
     *
     * @since 1.1.0
     *
     * @return FilePathInterface|null The client certificate path or null if not set.
     */
    public function getClientCertificate(): ?FilePathInterface;

    /**
     * Returns the client certificate password or null if not set.
     *
     * @since 1.3.0
     *
     * @return string|null The client certificate password or null if not set.
     */
    public function getClientCertificatePassword(): ?string;

    /**
     * Returns the client certificate type or null if not set.
     *
     * @since 1.3.0
     *
     * @return string|null
     */
    public function getClientCertificateType(): ?string;

    /**
     * Returns the client key path or null if not set.
     *
     * @since 1.1.0
     *
     * @return FilePathInterface|null The client key path or null if not set.
     */
    public function getClientKey(): ?FilePathInterface;

    /**
     * Returns the files to upload.
     *
     * @since 1.0.0
     *
     * @return array The files to upload.
     */
    public function getFiles(): array;

    /**
     * Returns the headers.
     *
     * @since 1.0.0
     *
     * @return string[] The headers.
     */
    public function getHeaders(): array;

    /**
     * Returns the method.
     *
     * @since 1.0.0
     *
     * @return string The method.
     */
    public function getMethod(): string;

    /**
     * Returns the post fields.
     *
     * @since 1.0.0
     *
     * @return array The post fields.
     */
    public function getPostFields(): array;

    /**
     * Returns the raw content.
     *
     * @since 1.0.0
     *
     * @return string The raw content.
     */
    public function getRawContent(): string;

    /**
     * Returns the url.
     *
     * @since 1.0.0
     *
     * @return UrlInterface The url.
     */
    public function getUrl(): UrlInterface;

    /**
     * Sets the certificate authority (CA) certificate path.
     *
     * @since 1.1.0
     *
     * @param FilePathInterface $caCertificate The certificate authority (CA) certificate path.
     *
     * @return $this
     */
    public function setCACertificate(FilePathInterface $caCertificate): self;

    /**
     * Sets the client certificate path.
     *
     * @since 1.1.0
     *
     * @param FilePathInterface $clientCertificate The client certificate path.
     *
     * @return $this
     */
    public function setClientCertificate(FilePathInterface $clientCertificate): self;

    /**
     * Sets the client certificate password.
     *
     * @since 1.3.0
     *
     * @param string $clientCertificatePassword The client certificate password.
     *
     * @return $this
     */
    public function setClientCertificatePassword(string $clientCertificatePassword): self;

    /**
     * Sets the client certificate type.
     *
     * @since 1.3.0
     *
     * @param string $clientCertificateType
     *
     * @return $this
     */
    public function setClientCertificateType(string $clientCertificateType): self;

    /**
     * Sets the client key path.
     *
     * @since 1.1.0
     *
     * @param FilePathInterface $clientKey The client key path.
     *
     * @return $this
     */
    public function setClientKey(FilePathInterface $clientKey): self;

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
    public function setFile(string $name, FilePathInterface $filePath): self;

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
    public function setPostField(string $name, string $value): self;

    /**
     * Sets the raw content.
     *
     * @since 1.0.0
     *
     * @param string $rawContent The raw content.
     *
     * @return $this
     */
    public function setRawContent(string $rawContent): self;
}
