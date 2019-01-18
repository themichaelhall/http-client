<?php
/**
 * This file is a part of the http-client package.
 *
 * Read more at https://github.com/themichaelhall/http-client
 */
declare(strict_types=1);

namespace MichaelHall\HttpClient;

use DataTypes\Interfaces\FilePathInterface;

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
     */
    public function __construct()
    {
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'http-client-cookies-');
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
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $request->getUrl()->__toString());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $request->getHeaders());
        curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookieFile);

        $this->setCurlContent($curl, $request);

        $result = curl_exec($curl);
        if ($result === false) {
            $error = curl_error($curl);
            curl_close($curl);

            return new HttpClientResponse(0, $error);
        }
        curl_close($curl);

        return $this->parseResult($result);
    }

    /**
     * Destructs the HTTP client.
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
     * Sets the CURL content from request.
     *
     * @param resource                   $curl    The CURL instance.
     * @param HttpClientRequestInterface $request The request.
     */
    private function setCurlContent($curl, HttpClientRequestInterface $request): void
    {
        $rawContent = $request->getRawContent();
        if ($rawContent !== '') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $rawContent);

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

        if (count($postFields) === 0) {
            return;
        }

        curl_setopt($curl, CURLOPT_POSTFIELDS, $hasFiles ? $postFields : http_build_query($postFields));
    }

    /**
     * Parses the CURL result into a HttpClientResponse.
     *
     * @param string $result The CURL result.
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
     * @var string My cookie file.
     */
    private $cookieFile;
}