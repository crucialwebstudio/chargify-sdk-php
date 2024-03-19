<?php
namespace Test\Helpers;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;

class MockResponse
{
    /**
     * Write a mock response file
     *
     * @param Response $response
     * @param string   $filename
     *
     * @code
     * $response = $subscription->getService()->getLastResponse();
     * MockResponse::write($response, 'subscription.success');
     */
    public static function write(Response $response, $filename)
    {
        $responseString = Psr7\Message::toString($response);

        // remove identifying information
        $responseString = preg_replace("/^Set-Cookie: (.*)$/im", "Set-Cookie: removed", $responseString);
        $responseString = preg_replace("/^X-Request-Id: (.*)$/im", "X-Request-Id: removed", $responseString);

        // write the mock response
        $handle = fopen(dirname(__DIR__) . '/mock/' . $filename, 'w+');
        fwrite($handle, $responseString);
        fclose($handle);
    }

    /**
     * Read a mock response file
     *
     * @param string $filename
     *
     * @return string
     */
    public static function read($filename)
    {
        return file_get_contents(dirname(__DIR__) . '/mock/' . $filename);
    }
}