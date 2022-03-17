<?php

/**
 * This file is a part of the http-client package.
 *
 * Read more at https://github.com/themichaelhall/http-client
 */

declare(strict_types=1);

namespace MichaelHall\HttpClient\RequestHandlers;

use CurlHandle;
use DataTypes\System\FilePathInterface;
use MichaelHall\HttpClient\HttpClientRequestInterface;
use MichaelHall\HttpClient\HttpClientResponse;
use MichaelHall\HttpClient\HttpClientResponseInterface;

/**
 * Curl request handler.
 *
 * @since 1.0.0
 */
class CurlRequestHandler implements RequestHandlerInterface
{
    /**
     * Constructs the request handler.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'http-client-cookies-');
        $this->options = [];
    }

    /**
     * Handles a request.
     *
     * @since 1.0.0
     *
     * @param HttpClientRequestInterface $request The request.
     *
     * @return HttpClientResponseInterface The response.
     */
    public function handleRequest(HttpClientRequestInterface $request): HttpClientResponseInterface
    {
        $curlHandle = curl_init();

        curl_setopt($curlHandle, CURLOPT_URL, $request->getUrl()->__toString());
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HEADER, true);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $request->getHeaders());
        curl_setopt($curlHandle, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($curlHandle, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($curlHandle, CURLOPT_ENCODING, '');

        $this->setPostFields($curlHandle, $request);
        $this->setCertificates($curlHandle, $request);
        $this->setOptions($curlHandle);

        $result = curl_exec($curlHandle);
        if ($result === false) {
            $error = curl_error($curlHandle);
            curl_close($curlHandle);

            return new HttpClientResponse(0, $error);
        }
        curl_close($curlHandle);

        return $this->parseResult($result);
    }

    /**
     * Sets a Curl option for the request.
     *
     * @since 1.4.0
     *
     * @param int   $option The CURLOPT_XXX option.
     * @param mixed $value  The option value.
     *
     * @return $this
     */
    public function setOption(int $option, mixed $value): self
    {
        $unsafeOption = self::UNSAFE_CURL_OPTIONS_OVERRIDE[$option] ?? null;
        if ($unsafeOption !== null) {
            trigger_error('Option "' . $unsafeOption . '" is used internally by CurlRequestHandler. Setting it manually may lead to unexpected results.', E_USER_WARNING);
        }

        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Sets the POST fields from request.
     *
     * @param CurlHandle                 $curlHandle The CURL handle.
     * @param HttpClientRequestInterface $request    The request.
     */
    private function setPostFields(CurlHandle $curlHandle, HttpClientRequestInterface $request): void
    {
        $rawContent = $request->getRawContent();
        if ($rawContent !== '') {
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $rawContent);

            return;
        }

        $postFields = [];
        $hasFiles = false;

        foreach ($request->getFiles() as $name => $filePath) {
            /** @var FilePathInterface $filePath */
            $curlFile = curl_file_create($filePath->__toString(), mime_content_type($filePath->__toString()), $filePath->getFilename());
            $postFields[$name] = $curlFile;
            $hasFiles = true;
        }

        foreach ($request->getPostFields() as $name => $value) {
            $postFields[$name] = $value;
        }

        if (count($postFields) !== 0) {
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $hasFiles ? $postFields : http_build_query($postFields));
        }
    }

    /**
     * Sets the certificates from request.
     *
     * @param CurlHandle                 $curlHandle The Curl handle.
     * @param HttpClientRequestInterface $request    The request.
     */
    private function setCertificates(CurlHandle $curlHandle, HttpClientRequestInterface $request): void
    {
        if ($request->getCACertificate() !== null) {
            curl_setopt($curlHandle, CURLOPT_CAINFO, $request->getCACertificate()->__toString());
        }

        if ($request->getClientCertificate() !== null) {
            curl_setopt($curlHandle, CURLOPT_SSLCERT, $request->getClientCertificate()->__toString());
        }

        if ($request->getClientCertificatePassword() !== null) {
            curl_setopt($curlHandle, CURLOPT_SSLCERTPASSWD, $request->getClientCertificatePassword());
        }

        if ($request->getClientCertificateType() !== null) {
            curl_setopt($curlHandle, CURLOPT_SSLCERTTYPE, $request->getClientCertificateType());
        }

        if ($request->getClientKey() !== null) {
            curl_setopt($curlHandle, CURLOPT_SSLKEY, $request->getClientKey()->__toString());
        }
    }

    /**
     * Sets the options for the request.
     *
     * @param CurlHandle $curlHandle The Curl handle.
     */
    private function setOptions(CurlHandle $curlHandle): void
    {
        foreach ($this->options as $optionName => $optionValue) {
            curl_setopt($curlHandle, $optionName, $optionValue);
        }
    }

    /**
     * Parses the Curl result into a HttpClientResponse.
     *
     * @param string $result The Curl result.
     *
     * @return HttpClientResponseInterface The response.
     */
    private function parseResult(string $result): HttpClientResponseInterface
    {
        $resultParts = explode("\r\n\r\n", $result, 2);

        $headers = explode("\r\n", $resultParts[0]);
        $statusLine = array_shift($headers);
        $statusLineParts = explode(' ', $statusLine);
        $httpCode = intval($statusLineParts[1]);
        if ($httpCode === 100) {
            return $this->parseResult($resultParts[1]);
        }

        $content = '';
        if (count($resultParts) > 1) {
            $content = $resultParts[1];
        }

        $response = new HttpClientResponse($httpCode, $content);
        foreach ($headers as $header) {
            $response->addHeader(trim($header));
        }

        return $response;
    }

    /**
     * Destructs the request handler.
     *
     * @since 1.0.0
     */
    public function __destruct()
    {
        if (file_exists($this->cookieFile)) {
            unlink($this->cookieFile);
        }
    }

    /**
     * @var string The cookie file.
     */
    private string $cookieFile;

    /**
     * @var array The Curl options for the request.
     */
    private array $options;

    /**
     * @var array Curl options that is used internally and should not be overridden without a warning.
     */
    private const UNSAFE_CURL_OPTIONS_OVERRIDE = [
        CURLOPT_CAINFO         => 'CURLOPT_CAINFO',
        CURLOPT_COOKIEFILE     => 'CURLOPT_COOKIEFILE',
        CURLOPT_COOKIEJAR      => 'CURLOPT_COOKIEJAR',
        CURLOPT_CUSTOMREQUEST  => 'CURLOPT_CUSTOMREQUEST',
        CURLOPT_HEADER         => 'CURLOPT_HEADER',
        CURLOPT_HTTPHEADER     => 'CURLOPT_HTTPHEADER',
        CURLOPT_POSTFIELDS     => 'CURLOPT_POSTFIELDS',
        CURLOPT_RETURNTRANSFER => 'CURLOPT_RETURNTRANSFER',
        CURLOPT_SSLCERT        => 'CURLOPT_SSLCERT',
        CURLOPT_SSLCERTPASSWD  => 'CURLOPT_SSLCERTPASSWD',
        CURLOPT_SSLCERTTYPE    => 'CURLOPT_SSLCERTTYPE',
        CURLOPT_SSLKEY         => 'CURLOPT_SSLKEY',
        CURLOPT_URL            => 'CURLOPT_URL',
    ];
}
