<?php

/**
 * Copyright 2011 Crucial Web Studio, LLC or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * https://raw.githubusercontent.com/chargely/chargify-sdk-php/master/LICENSE.md
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Crucial\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use Crucial\Service\Chargify;
use Crucial\Service\ChargifyV2\Call;
use Crucial\Service\ChargifyV2\Direct;
use Crucial\Service\ChargifyV2\Exception\BadMethodCallException;
use Crucial\Service\ChargifyV2\Exception\InvalidArgumentException;

class ChargifyV2
{
    /**
     * The base URL for all api calls
     *
     * @var string
     */
    protected $baseUrl = 'https://api.chargify.com/api/v2/';

    /**
     * Your api_d
     *
     * @var string
     */
    protected $apiId;

    /**
     * Your api password
     *
     * @var string
     */
    protected $apiPassword;

    /**
     * Secret key
     *
     * @var string
     */
    protected $apiSecret;

    /**
     * Config used in constructor.
     *
     * @var array
     */
    protected $config;

    /**
     * Last response received by the client
     *
     * @var Response|false
     */
    protected $lastResponse;

    /**
     * Guzzle http client
     *
     * @var Client
     */
    protected $httpClient;

    /**
     * Initialize the service
     *
     * @param array $config
     */
    public function __construct($config)
    {
        // store a copy
        $this->config = $config;

        // set individual properties
        $this->apiId       = $config['api_id'];
        $this->apiPassword = $config['api_password'];
        $this->apiSecret   = $config['api_secret'];

        $httpHandler = isset($config['GuzzleHttp\Client']['handler']) ? $config['GuzzleHttp\Client']['handler'] : null;

        if (empty($httpHandler)) {
            $httpHandler = HandlerStack::create();
        }
        if (! $httpHandler instanceof HandlerStack) {
            throw new InvalidArgumentException("config['GuzzleHttp\Client']['handler'] is not an instance of ".HandlerStack::class);
        }

        // set up http client
        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'handler'         => $httpHandler,
            'timeout'         => 10,
            'allow_redirects' => false,
            'auth'            => [$this->apiId, $this->apiPassword],
            'headers'         => [
                'User-Agent' => 'chargify-sdk-php/' . Chargify::VERSION . ' (https://github.com/chargely/chargify-sdk-php)',
                'Accept'     => 'application/json'
            ]
        ]);
    }

    /**
     * Get the base URL for all requests made to the api.
     *
     * Does not contain a trailing slash.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Getter for api ID
     *
     * @return string
     */
    public function getApiId()
    {
        return $this->apiId;
    }

    /**
     * Getter for api secret.
     *
     * Be careful not to expose this to anyone, especially in your html.
     *
     * @return string
     */
    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    /**
     * Getter for api password.
     *
     * Be careful not to expose this to anyone, especially in your html.
     *
     * @return string
     */
    public function getApiPassword()
    {
        return $this->apiPassword;
    }

    /**
     * Returns config sent in constructor
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Getter for $this->httpClient
     *
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Send the request to Chargify
     *
     * @param string $path   URL path we are requesting such as: /subscriptions/<subscription_id>/adjustments
     * @param string $method GET, POST, PUT, DELETE
     * @param string $rawData
     * @param array  $params
     *
     * @return Response|false Response object or false if there was no response (networking error, timeout, etc.)
     */
    public function request($path, $method, $rawData = null, $params = [])
    {
        $method  = strtoupper($method);
        $path    = ltrim($path, '/');
        $client  = $this->getHttpClient();
        $method  = strtoupper($method);
        $options = [
            'query' => $params,
            'body' => null,
        ];

        $request = new Request($method, $path);

        if (in_array($method, array('POST', 'PUT'))) {
            if (null === $rawData) {
                throw new BadMethodCallException('You must send raw data in a POST or PUT request');
            }
        }

        if (!empty($rawData)) {
            $options['body'] = Psr7\Utils::streamFor($rawData);
        }

        try {
            $response = $client->send($request, $options);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
            } else {
                $response = false;
            }
        }

        $this->lastResponse = $response;

        return $response;
    }

    /**
     * @return Response|false
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Helper for instantiating an instance of Direct
     *
     * @return Direct
     */
    public function direct()
    {
        return new Direct($this);
    }

    /**
     * Helper for instantiating an instance of Call
     *
     * @return Call
     */
    public function call()
    {
        return new Call($this);
    }
}
