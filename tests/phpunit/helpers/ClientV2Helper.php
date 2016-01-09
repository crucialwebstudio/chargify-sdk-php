<?php

use Crucial\Service\ChargifyV2;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;

class ClientV2Helper
{
    /**
     * Get a Chargify client instance
     *
     * @param string $mockResponseFile Filename containing mocked response
     * @param string $env              (test|dev)
     *
     * @return ChargifyV2
     */
    public static function getInstance($mockResponseFile = null, $env = 'test')
    {
        $config = array();
        switch ($env) {
            case 'test':
                $config = require dirname(__DIR__) . '/configs/ClientV2Config.Test.php';
                break;
            case 'dev':
                $config = require dirname(__DIR__) . '/configs/ClientV2Config.Dev.php';
                break;
        }

        $chargify = new ChargifyV2($config);

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