<?php

declare(strict_types=1);

namespace MichaelHall\HttpClient\Tests\Helpers\Fakes {

    use DataTypes\Url;

    /**
     * Helper class for faking curl_* methods.
     */
    class FakeCurl
    {
        /**
         * Disable fake curl.
         */
        public static function disable(): void
        {
            self::$isEnabled = false;
        }

        /**
         * Enable fake curl.
         */
        public static function enable(): void
        {
            self::$isEnabled = true;
            self::$options = [];
            self::$error = '';
        }

        /**
         * @return bool True if fake curl is enabled, false otherwise.
         */
        public static function isEnabled(): bool
        {
            return self::$isEnabled;
        }

        /**
         * Executes a request.
         *
         * This method returns a response with content from a fake server.
         *
         * @return string|bool The result or false if request failed.
         */
        public static function exec()
        {
            $url = Url::parse(self::$options[CURLOPT_URL]);

            $hostname = $url->getHost()->getHostname();
            if ($hostname->__toString() !== 'example.com') {
                self::$error = 'Failed to connect to ' . $hostname . ': Connection refused';

                return false;
            }

            $path = $url->getPath();
            switch ($path->__toString()) {
                case '/':
                    $responseCode = 200;
                    $responseHeaders = [];
                    $responseText = 'Hello World!';
                    break;

                case '/continue':
                    $responseCode = 100;
                    $responseHeaders = [];
                    $responseText = 'Hello World!';
                    break;

                case '/response-header':
                    $responseCode = 200;
                    $responseHeaders = [urldecode($url->getQueryString())];
                    $responseText = 'Hello World!';
                    break;

                default:
                    $responseCode = 404;
                    $responseHeaders = [];
                    $responseText = 'Not found';
                    break;
            }

            $result = [];

            if ($responseCode === 100) {
                $result[] = 'HTTP/1.1 100 Continue';
                $result[] = '';

                $responseCode = 200;
            }

            $result[] = 'HTTP/1.1 ' . $responseCode . ' Ok';
            foreach ($responseHeaders as $responseHeader) {
                $result[] = $responseHeader;
            }
            $result[] = '';

            $result[] = $responseText;

            return implode("\r\n", $result);
        }

        /**
         * Sets an option.
         *
         * @param int   $option The option.
         * @param mixed $value  The option value.
         *
         * @return bool Always true.
         */
        public static function setOption(int $option, $value): bool
        {
            self::$options[$option] = $value;

            return true;
        }

        /**
         * Returns an option or null if option is not set.
         *
         * @param int $option The option.
         *
         * @return mixed The option value or null.
         */
        public static function getOption(int $option)
        {
            return self::$options[$option] ?? null;
        }

        /**
         * Returns the last error.
         *
         * @return string The last error.
         */
        public static function getError(): string
        {
            return self::$error;
        }

        /**
         * @var bool True if fake curl is enabled, false otherwise.
         */
        private static $isEnabled = false;

        /**
         * @var array The options.
         */
        private static $options = [];

        /**
         * @var string The last error.
         */
        private static $error = '';
    }
}

namespace MichaelHall\HttpClient\RequestHandlers {

    use MichaelHall\HttpClient\Tests\Helpers\Fakes\FakeCurl;

    /**
     * Fakes the curl_setopt method.
     *
     * @param mixed $handle The handle.
     * @param int   $option The option.
     * @param mixed $value  The option value.
     *
     * @return bool True on success and false on failure.
     */
    function curl_setopt($handle, int $option, $value): bool
    {
        if (FakeCurl::isEnabled()) {
            return FakeCurl::setOption($option, $value);
        }

        return \curl_setopt($handle, $option, $value);
    }

    /**
     * Fakes the curl_exec method.
     *
     * @param mixed $handle The handle.
     *
     * @return string|bool The result or false if request failed.
     */
    function curl_exec($handle)
    {
        if (FakeCurl::isEnabled()) {
            return FakeCurl::exec();
        }

        return \curl_exec($handle);
    }

    /**
     * Fakes the curl_error method.
     *
     * @param mixed $handle The handle.
     *
     * @return string The error message.
     */
    function curl_error($handle): string
    {
        if (FakeCurl::isEnabled()) {
            return FakeCurl::getError();
        }

        return \curl_error($handle);
    }
}
