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
use Crucial\Service\Chargify\Exception\BadMethodCallException;
use Crucial\Service\Chargify\Adjustment;
use Crucial\Service\Chargify\Charge;
use Crucial\Service\Chargify\Component;
use Crucial\Service\Chargify\Coupon;
use Crucial\Service\Chargify\Customer;
use Crucial\Service\Chargify\Event;
use Crucial\Service\Chargify\Product;
use Crucial\Service\Chargify\Refund;
use Crucial\Service\Chargify\Statement;
use Crucial\Service\Chargify\Stats;
use Crucial\Service\Chargify\Subscription;
use Crucial\Service\Chargify\Transaction;
use Crucial\Service\Chargify\Webhook;

class Chargify
{
    /**
     * Version
     */
    const VERSION = '0.1.1';

    /**
     * Guzzle http client
     *
     * @var Client
     */
    private $httpClient;

    /**
     * The complete hostname; e.g. "my-app-subdomain.chargify.com",
     * not just "my-app-subdomain"
     *
     * @var string
     */
    protected $hostname;

    /**
     * Your http authentication password. The password is always "x".
     *
     * @var string
     */
    protected $password = 'x';

    /**
     * Your api key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Shared key
     *
     * @var string
     */
    protected $sharedKey;

    /**
     * Timeout
     *
     * @var int
     */
    protected $timeout = 10;

    /*
     * json
     *
     * @var string
     */
    protected $format = 'json';

    /**
     * Config used in constructor.
     *
     * @var array
     */
    protected $config;

    /**
     * @var Response|false|null
     */
    protected $lastResponse;

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
        $this->hostname  = trim($config['hostname'], '/');
        $this->apiKey    = $config['api_key'];
        $this->sharedKey = $config['shared_key'];

        if (!empty($config['timeout'])) {
            $this->timeout   = $config['timeout'];
        }


        $this->httpClient = new Client([
            'base_uri'        => 'https://' . $this->hostname . '/',
            'handler'         => HandlerStack::create(),
            'timeout'         => $this->timeout,
            'allow_redirects' => false,
            'auth'            => [$this->apiKey, $this->password],
            'headers'         => [
                'User-Agent'   => 'chargify-sdk-php/' . self::VERSION . ' (https://github.com/chargely/chargify-sdk-php)',
                'Content-Type' => 'application/' . $this->format
            ]
        ]);
    }

    /**
     * @return Client
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
        return $this->config;
    }

    /**
     * Send the request to Chargify
     *
     * @param string $path   URL path we are requesting such as: /subscriptions/<subscription_id>/adjustments
     * @param string $method GET, POST, PUT, DELETE
     * @param string $rawData
     * @param array  $params
     *
     * @return Response|FALSE Response object or FALSE if there was no response (networking error, timeout, etc.)
     */
    public function request($path, $method, $rawData = null, $params = [])
    {
        $method  = strtoupper($method);
        $path    = ltrim($path, '/');
        $path    = $path . '.' . $this->format;
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
     * @return Response
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
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
