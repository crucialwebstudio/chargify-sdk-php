<?php

use Crucial\Service\Chargify;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;

class ClientHelper
{
    /**
     * Get a Chargify client instance
     *
     * @param string $mockResponseFile Filename containing mocked response
     * @param string $env              (test|dev)
     *
     * @return Chargify
     */
    public static function getInstance($mockResponseFile = null, $env = 'test')
    {
        $config = array();
        switch ($env) {
            case 'test':
                $config = require dirname(__DIR__) . '/configs/ClientConfig.Test.php';
                break;
            case 'dev':
                $config = require dirname(__DIR__) . '/configs/ClientConfig.Dev.php';
                break;
        }

        $chargify = new Chargify($config);

        if (!empty($mockResponseFile)) {
            $mock     = new MockHandler([
                Psr7\parse_response(MockResponse::read($mockResponseFile))
            ]);
            $handler  = HandlerStack::create($mock);
            $chargify->getHttpClient()->getConfig('handler')->setHandler($handler);
        }

        return $chargify;
    }
}