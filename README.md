# Http Client

[![Tests](https://github.com/themichaelhall/http-client/workflows/Tests/badge.svg?branch=master)](https://github.com/themichaelhall/http-client/actions)
[![codecov.io](https://codecov.io/gh/themichaelhall/http-client/coverage.svg?branch=master)](https://codecov.io/gh/themichaelhall/http-client?branch=master)
[![StyleCI](https://styleci.io/repos/166465522/shield?style=flat&branch=master)](https://styleci.io/repos/166465522)
[![License](https://poser.pugx.org/michaelhall/http-client/license)](https://packagist.org/packages/michaelhall/http-client)
[![Latest Stable Version](https://poser.pugx.org/michaelhall/http-client/v/stable)](https://packagist.org/packages/michaelhall/http-client)
[![Total Downloads](https://poser.pugx.org/michaelhall/http-client/downloads)](https://packagist.org/packages/michaelhall/http-client)

A simple HTTP client.

## Requirements

- PHP >= 7.3

## Install with Composer

``` bash
$ composer require michaelhall/http-client
```

## Basic usage

### Get a web page

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use DataTypes\Url;
use MichaelHall\HttpClient\HttpClient;
use MichaelHall\HttpClient\HttpClientRequest;

$url = Url::parse('https://example.com/');
$client = new HttpClient();
$request = new HttpClientRequest($url);
$response = $client->send($request);

// Prints "success" if request was successful, "fail" otherwise.
echo $response->isSuccessful() ? 'success' : 'fail';

// Prints the response content.
echo $response->getContent();

// Prints the http code, e.g. 200.
echo $response->getHttpCode();

// Prints the headers.
foreach ($response->getHeaders() as $header) {
    echo $header;
}
```

### Customize the request

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use DataTypes\FilePath;
use DataTypes\Url;
use MichaelHall\HttpClient\HttpClientRequest;

$url = Url::parse('https://example.com/');

// Set the method.
$request = new HttpClientRequest($url, 'POST');

// Set a POST field.
$request->setPostField('Foo', 'Bar');

// Set a file.
$request->setFile('Baz', FilePath::parse('/path/to/file'));

// Add a header.
$request->addHeader('Content-type: application/json');

// Set raw content.
$request->setRawContent('{"Foo": "Bar"}');

// Client certificates.
$request->setCACertificate(FilePath::parse('/path/to/ca-certificate.pem'));
$request->setClientCertificate(FilePath::parse('/path/to/client-certificate.pem'));
$request->setClientKey(FilePath::parse('/path/to/client-key.pem'));
```

### Create a custom request handler

A custom/fake request handler may be used and injected in the ```HttpClient``` constructor. To do this, the request handler must implement ```RequestHandlerInterface``` and the ```handleRequest``` method.

```php
<?php

use DataTypes\Url;
use MichaelHall\HttpClient\HttpClient;
use MichaelHall\HttpClient\HttpClientRequest;
use MichaelHall\HttpClient\HttpClientRequestInterface;
use MichaelHall\HttpClient\HttpClientResponse;
use MichaelHall\HttpClient\HttpClientResponseInterface;
use MichaelHall\HttpClient\RequestHandlers\RequestHandlerInterface;

require_once __DIR__ . '/vendor/autoload.php';

class FakeRequestHandler implements RequestHandlerInterface
{
    public function handleRequest(HttpClientRequestInterface $request): HttpClientResponseInterface
    {
        if ($request->getUrl()->getPath()->__toString() === '/foo') {
            return new HttpClientResponse(200, 'Hello World');
        }

        return new HttpClientResponse(404);
    }
}

// Inject the custom request handler in constructor.
$client = new HttpClient(new FakeRequestHandler());

$request = new HttpClientRequest(Url::parse('https://example.com/foo'));
$response = $client->send($request);

// Prints "Hello World".
echo $response->getContent();

$request = new HttpClientRequest(Url::parse('https://example.com/'));
$response = $client->send($request);

// Prints "404".
echo $response->getHttpCode();
```

## License

MIT