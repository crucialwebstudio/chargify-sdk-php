<?php

/**
 * Copyright 2011 Crucial Web Studio, LLC or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * https://raw.githubusercontent.com/crucialwebstudio/chargify-sdk-php/master/LICENSE
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Crucial\Service;

use GuzzleHttp\Client,
    GuzzleHttp\Stream\Stream,
    GuzzleHttp\Exception\RequestException,
    Crucial\Service\Chargify\Exception\BadMethodCallException,
    Crucial\Service\Chargify\Adjustment,
    Crucial\Service\Chargify\Charge,
    Crucial\Service\Chargify\Component,
    Crucial\Service\Chargify\Coupon,
    Crucial\Service\Chargify\Customer,
    Crucial\Service\Chargify\Event,
    Crucial\Service\Chargify\Product,
    Crucial\Service\Chargify\Refund,
    Crucial\Service\Chargify\Statement,
    Crucial\Service\Chargify\Stats,
    Crucial\Service\Chargify\Subscription,
    Crucial\Service\Chargify\Transaction,
    Crucial\Service\Chargify\Webhook;

class Chargify
{
    /**
     * Guzzle http client
     *
     * @var \GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * The complete hostname; e.g. "my-app-subdomain.chargify.com",
     * not just "my-app-subdomain"
     *
     * @var string
     */
    protected $_hostname;

    /**
     * Your http authentication password. The password is always "x".
     *
     * @var string
     */
    protected $_password = 'x';

    /**
     * Your api key
     *
     * @var string
     */
    protected $_apiKey;

    /**
     * Shared key
     *
     * @var string
     */
    protected $_sharedKey;

    /**
     * json
     *
     * @var string
     */
    protected $_format = 'json';

    /**
     * Config used in constructor.
     *
     * @var array
     */
    protected $_config;

    /**
     * @var \GuzzleHttp\Message\Response|false|null
     */
    protected $_lastResponse;

    /**
     * Initialize the service
     *
     * @param array $config
     */
    public function __construct($config)
    {
        // store a copy
        $this->_config = $config;

        // set individual properties
        $this->_hostname  = trim($config['hostname'], '/');
        $this->_apiKey    = $config['api_key'];
        $this->_sharedKey = $config['shared_key'];

        $this->httpClient = new Client([
            'base_url' => 'https://' . $this->_hostname . '/',
            'defaults' => [
                'timeout'         => 10,
                'allow_redirects' => false,
                'auth'            => [$this->_apiKey, $this->_password],
                'headers'         => [
                    'User-Agent'   => 'chargify-sdk-php/1.0 (https://github.com/chargely/chargify-sdk-php)',
                    'Content-Type' => 'application/' . $this->_format
                ]
            ]
        ]);
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Returns config sent in constructor
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Send the request to Chargify
     *
     * @param string $path   URL path we are requesting such as: /subscriptions/<subscription_id>/adjustments
     * @param string $method GET, POST, PUST, DELETE
     * @param string $rawData
     * @param array  $params
     *
     * @return \GuzzleHttp\Message\Response|FALSE Response object or FALSE if there was no response (networking error,
     *                                            timeout, etc.)
     */
    public function request($path, $method, $rawData = NULL, $params = array())
    {
        $method  = strtoupper($method);
        $path    = ltrim($path, '/');
        $path    = $path . '.' . $this->_format;
        $client  = $this->getHttpClient();
        $request = $client->createRequest($method, $path);

        // set headers if POST or PUT
        if (in_array($method, array('POST', 'PUT'))) {
            if (NULL === $rawData) {
                throw new BadMethodCallException('You must send raw data in a POST or PUT request');
            }

            if (!empty($params)) {
                $request->setQuery($params);
            }

            $request->setBody(Stream::factory($rawData));
        }

        // set headers if GET or DELETE
        if (in_array($method, array('GET', 'DELETE'))) {

            if (!empty($rawData)) {
                $request->setBody(Stream::factory($rawData));
            }

            if (!empty($params)) {
                $request->setQuery($params);
            }
        }

        try {
            $response = $client->send($request);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
            } else {
                $response = false;
            }
        }

        $this->_lastResponse = $response;

        return $response;
    }

    /**
     * @return \GuzzleHttp\Message\Response
     */
    public function getLastResponse()
    {
        return $this->_lastResponse;
    }

    /**
     * Helper for instantiating an instance of Customer
     *
     * @return Customer
     */
    public function customer()
    {
        return new Customer($this);
    }

    /**
     * Helper for instantiating an instance of Subscription
     *
     * @return Subscription
     */
    public function subscription()
    {
        return new Subscription($this);
    }

    /**
     * Helper for instantiating an instance of Product
     *
     * @return Product
     */
    public function product()
    {
        return new Product($this);
    }

    /**
     * Helper for instantiating an instance of Adjustment
     *
     * @return Adjustment
     */
    public function adjustment()
    {
        return new Adjustment($this);
    }

    /**
     * Helper for instantiating an instance of Charge
     *
     * @return Charge
     */
    public function charge()
    {
        return new Charge($this);
    }

    /**
     * Helper for instantiating an instance of Component
     *
     * @return Component
     */
    public function component()
    {
        return new Component($this);
    }

    /**
     * Helper for instantiating an instance of Coupon
     *
     * @return Coupon
     */
    public function coupon()
    {
        return new Coupon($this);
    }

    /**
     * Helper for instantiating an instance of Transaction
     *
     * @return Transaction
     */
    public function transaction()
    {
        return new Transaction($this);
    }

    /**
     * Helper for instantiating an instance of Refund
     *
     * @return Refund
     */
    public function refund()
    {
        return new Refund($this);
    }

    /**
     * Helper for instantiating an instance of Statement
     *
     * @return Statement
     */
    public function statement()
    {
        return new Statement($this);
    }

    /**
     * Helper for instantiating an instance of Event
     *
     * @return Event
     */
    public function event()
    {
        return new Event($this);
    }

    /**
     * Helper for instantiating an instance of Webhook
     *
     * @return Webhook
     */
    public function webhook()
    {
        return new Webhook($this);
    }

    /**
     * Helper for instantiating an instance of Stats
     *
     * @return Stats
     */
    public function stats()
    {
        return new Stats($this);
    }
}