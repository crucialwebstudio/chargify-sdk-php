<?php
namespace Test\Helpers;

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

        if (!empty($mockResponseFile)) {
            $mock     = new MockHandler([
                Psr7\Message::parseResponse(MockResponse::read($mockResponseFile))
            ]);
            
            // Override default GuzzleHttp Client's handler by a mock
            $handler  = HandlerStack::create($mock);
            $config['GuzzleHttp\Client'] = [
                'handler' => $handler
            ];
        }

        $chargify = new ChargifyV2($config);

        return $chargify;
    }
}