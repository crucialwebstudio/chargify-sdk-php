<?php

use Crucial\Service\Chargify;

class ClientHelper
{
    /**
     * Get a Chargify client instance
     *
     * @param string $env (test|dev)
     *
     * @return Chargify
     */
    public static function getInstance($env = 'test')
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

        return new Chargify($config);
    }
}