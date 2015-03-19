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

use Crucial\Service\ChargifyV2\Exception\BadMethodCallException,
    Crucial\Service\ChargifyV2\Call,
    Crucial\Service\ChargifyV2\Direct;

class ChargifyV2
{
    /**
     * The base URL for all api calls. NO TRAILING SLASH!
     *
     * @var string
     */
    protected $_baseUrl = 'https://api.chargify.com/api/v2';

    /**
     * Your api_d
     *
     * @var string
     */
    protected $_apiId;

    /**
     * Your api password
     *
     * @var string
     */
    protected $_apiPassword;

    /**
     * Secret key
     *
     * @var string
     */
    protected $_apiSecret;

    /**
     * response expected from API
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
     * Http client
     *
     * @var \Zend_Http_Client
     */
    protected $_client;

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
        $this->_apiId       = $config['api_id'];
        $this->_apiPassword = $config['api_password'];
        $this->_apiSecret   = $config['api_secret'];

        // set up http client
        $this->_client = new \Zend_Http_Client();

        /**
         * @todo should these be config options?
         */
        $this->_client->setConfig(
            array(
                'maxredirects' => 0,
                'timeout'      => 30,
                'keepalive'    => TRUE,
                'useragent'    => 'chargify-sdk-php/1.0 (https://github.com/crucialwebstudio/chargify-sdk-php)'
            )
        );

        // username, password for http authentication
        $this->_client->setAuth($this->_apiId, $this->_apiPassword, \Zend_Http_Client::AUTH_BASIC);
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
        return $this->_baseUrl;
    }

    /**
     * Getter for api ID
     *
     * @return string
     */
    public function getApiId()
    {
        return $this->_apiId;
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
        return $this->_apiSecret;
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
     * Getter for $this->_client
     *
     * @return \Zend_Http_Client
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Send the request to Chargify
     *
     * @param string $path   URL path we are requesting such as: /subscriptions/<subscription_id>/adjustments
     * @param string $method GET, POST, PUST, DELETE
     * @param string $rawData
     * @param array  $params
     *
     * @return \Zend_Http_Response
     */
    public function request($path, $method, $rawData = NULL, $params = array())
    {
        $method = strtoupper($method);
        $path   = ltrim($path, '/');

        $client = $this->getClient();
        $client->setUri($this->_baseUrl . '/' . $path . '.' . $this->_format);

        // unset headers. they don't get cleared between requests
        $client->setHeaders(
            array(
                'Content-Type' => NULL,
                'Accept'       => NULL
            )
        );

        // clear parameters
        $client->resetParameters();

        $client->setHeaders(
            array(
                'Content-Type' => 'application/' . $this->_format
            )
        );

        // set headers if POST or PUT
        if (in_array($method, array('POST', 'PUT'))) {
            if (NULL === $rawData) {
                throw new BadMethodCallException('You must send raw data in a POST or PUT request');
            }

            $client->setHeaders(
                array(
                    'Content-Type' => 'application/' . $this->_format
                )
            );

            if (!empty($params)) {
                $client->setParameterGet($params);
            }

            $client->setRawData($rawData, 'application/' . $this->_format);
        }

        // set headers if GET or DELETE
        if (in_array($method, array('GET', 'DELETE'))) {
            $client->setHeaders(
                array(
                    'Accept' => 'application/' . $this->_format
                )
            );

            if (!empty($rawData)) {
                $client->setRawData($rawData, 'application/' . $this->_format);
            }

            if (!empty($params)) {
                foreach ($params as $k => $v) {
                    /**
                     * test for array and adjust URI accordingly
                     * this is needed for ?kinds[]=charge&kinds[]=info since \Zend_Http_Client
                     * doesn't handle this well with setParameterGet()
                     */
                    if (is_array($v)) {
                        $uri = '?';
                        foreach ($v as $value) {
                            $uri .= $k . '[]=' . $value . '&';
                        }
                        $uri = $client->getUri(TRUE) . trim($uri, '&');
                        $client->setUri($uri);
                    } else {
                        $client->setParameterGet($k, $v);
                    }
                }
            }
        }

        $response = $client->request($method);

        return $response;
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