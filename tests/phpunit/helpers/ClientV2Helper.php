<?php

use Crucial\Service\ChargifyV2;

class ClientV2Helper
{
    /**
     * Get a Chargify client instance
     *
     * @param string $env (test|dev)
     *
     * @return ChargifyV2
     */
    public static function getInstance($env = 'test')
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

        return new ChargifyV2($config);
    }
}