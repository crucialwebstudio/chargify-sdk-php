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

namespace Crucial\Service\ChargifyV2;

use Crucial\Service\ChargifyV2;

class Direct
{
    /**
     * Instance of ChargifyV2 sent in constructor
     *
     * @var ChargifyV2
     */
    protected $_service;

    /**
     * Unix timestamp
     *
     * Sent as secure[timestamp] in the request
     *
     * @var int
     */
    protected $_timeStamp;

    /**
     * 40 character nonce
     *
     * Sent as secure[nonce] in the request
     *
     * @var string
     */
    protected $_nonce;

    /**
     * Array of tamper-proof data to send to Chargify
     *
     * Sent as secure[data] in the request
     *
     * @var array
     */
    protected $_data = array();

    /**
     * The hash_hmac() signature of the request
     *
     * Sent as secure[signature] in the request
     *
     * @var string
     */
    protected $_requestSignature;

    /**
     * The URL to redirect to after submission
     *
     * @var string
     */
    protected $_redirect;

    /**
     * Simply stores service instance
     *
     * @param ChargifyV2 $service
     */
    public function __construct(ChargifyV2 $service)
    {
        $this->_service = $service;
    }

    /**
     * Get Crucial_Service_ChargifyV2 instance sent in constructor
     *
     * @return ChargifyV2
     */
    public function getService()
    {
        return $this->_service;
    }

    /**
     * Get the API ID which is safe to expose publicly; e.g. in hidden form fields
     *
     * @return string
     */
    public function getApiId()
    {
        return $this->getService()->getApiId();
    }

    /**
     * Tamper-proof data that you want to send to Chargify
     *
     * @param array $data
     */
    public function setData($data = array())
    {
        /**
         * Since we base our signature off of the secure[data] parameter it cannot
         * be changed once the signature has been generated.
         */
        if (isset($this->_requestSignature)) {
            throw new Crucial_Service_ChargifyV2_Exception('The signature for this request has already been generated.');
        }

        $this->_data = $data;
        $this->_mergeRedirect();
    }

    /**
     * Getter for $this->_data
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Get string value to send in secure[data]
     *
     * This is the query string equivalent of $this->_data; generated with
     * http_build_query()
     *
     * @return string
     */
    public function getDataString()
    {
        // percent encoded
        $string = http_build_query($this->_data);
        $string = str_replace(array('%5B', '%5D'), array('[', ']'), $string);

        return $string;
    }

    /**
     * Get string value to send in secure[data] using &amp; as the arg separator
     *
     * @return string
     */
    public function getDataStringEncoded()
    {
        // percent encoded
        $string = http_build_query($this->_data, '', '&amp;');
        $string = str_replace(array('%5B', '%5D'), array('[', ']'), $string);

        return $string;
    }

    /**
     * Set the URI where Chargify should redirect
     *
     * @return void
     */
    public function setRedirect($redirect)
    {
        /**
         * Changing the redirect also changes the secure[data] parameter. Since we
         * base our signature off of this it cannot be changed once the signature
         * has been generated.
         */
        if (isset($this->_requestSignature)) {
            throw new Crucial_Service_ChargifyV2_Exception('The signature for this request has already been generated.');
        }

        $this->_redirect = $redirect;
        $this->_mergeRedirect();
    }

    /**
     * The redirect_uri must be sent with secure[data], so we merge it in with
     * $this->_data
     *
     * @return void
     */
    protected function _mergeRedirect()
    {
        if (!empty($this->_redirect)) {
            $this->_data = array_merge_recursive($this->_data, array('redirect_uri' => $this->_redirect));
        }
    }

    /**
     * Get the URI where Chargify will redirect
     *
     * @return string
     */
    public function getRedirect()
    {
        return $this->_redirect;
    }

    /**
     * Get a Unix timestamp
     *
     * @return int
     */
    public function getTimeStamp()
    {
        if (empty($this->_timeStamp)) {
            $this->_timeStamp = time();
        }

        return $this->_timeStamp;
    }

    /**
     * Get a 40 character string to use as a nonce
     *
     * This is the sha1 of a random string. sha1 gives us 40 characters which is
     * the length required by Chargify Direct.
     *
     * @return string
     */
    public function getNonce()
    {
        if (empty($this->_nonce)) {
            // generate a random string
            $bits   = 256;
            $bytes  = ceil($bits / 8);
            $string = '';
            for ($i = 0; $i < $bytes; $i++) {
                $string .= chr(mt_rand(0, 255));
            }
            // sha1 hash
            $this->_nonce = hash('sha1', $string);
        }

        return $this->_nonce;
    }

    /**
     * Calculate the hmac-sha1 signature of the request
     *
     * This will be sent as secure[signature] in the request
     *
     * @return string
     */
    public function getRequestSignature()
    {
        if (empty($this->_requestSignature)) {
            $string                  = $this->getApiId()
                . $this->getTimeStamp()
                . $this->getNonce()
                . $this->getDataString();
            $this->_requestSignature = hash_hmac('sha1', $string, $this->getService()->getApiSecret());
        }

        return $this->_requestSignature;
    }

    /**
     * Calculate the hmac-sha1 signature of the response
     *
     * We will use this calculation to compare against the signature we receive
     * back from Chargify after the redirect.
     *
     * @return string
     * @see isValidResponseSignature()
     */
    public function getResponseSignature()
    {
        $string = $_GET['api_id']
            . $_GET['timestamp']
            . $_GET['nonce']
            . $_GET['status_code']
            . $_GET['result_code']
            . $_GET['call_id'];

        return hash_hmac('sha1', $string, $this->getService()->getApiSecret());
    }

    /**
     * Test if response signature matches what we expect
     *
     * This should be called after the redirect from Chargify to verify the
     * response signature. The signature to compare against will be available in
     * the query string $_GET['signature']
     *
     * @return bool
     * @see getResponseSignature()
     */
    public function isValidResponseSignature()
    {
        return ($_GET['signature'] == $this->getResponseSignature());
    }

    /**
     * The <form action=""> to use for signups
     *
     * @return string
     */
    public function getSignupAction()
    {
        return $this->getService()->getBaseUrl() . '/signups';
    }

    /**
     * The <form action=""> to use for credit card updates
     *
     * @param int $subscriptionId The ID of the subscription you want to update
     *
     * @return string
     */
    public function getCardUpdateAction($subscriptionId)
    {
        return $this->getService()->getBaseUrl() . '/subscriptions/' . (string)$subscriptionId . '/card_update';
    }

    /**
     * Get the html for all hidden fields
     *
     * @return string
     */
    public function getHiddenFields()
    {
        $apiId     = '<input type="hidden" name="secure[api_id]"    value="' . $this->getApiId() . '" />';
        $timestamp = '<input type="hidden" name="secure[timestamp]" value="' . $this->getTimeStamp() . '" />';
        $nonce     = '<input type="hidden" name="secure[nonce]"     value="' . $this->getNonce() . '" />';
        $data      = '<input type="hidden" name="secure[data]"      value="' . $this->getDataStringEncoded() . '" />';
        $signature = '<input type="hidden" name="secure[signature]" value="' . $this->getRequestSignature() . '" />';

        return "$apiId\n$timestamp\n$nonce\n$data\n$signature\n";
    }

    /**
     * Check if Chargify Direct credentials are correct
     *
     * @return boolean
     */
    public function authTest()
    {
        // set a fake redirect URL. Chargify will 500 on us if we don't have a redirect URL
        $this->setRedirect('https://foo.com');

        $postParams = array(
            'secure' => array(
                'api_id'    => $this->getApiId(),
                'timestamp' => $this->getTimeStamp(),
                'nonce'     => $this->getNonce(),
                'signature' => $this->getRequestSignature(),
                'data'      => $this->getDataStringEncoded()
            )
        );

        $client = $this->getService()->getClient();
        $client->setUri($this->getSignupAction());
        $client->setParameterPost($postParams);

        $response = $client->request(\Zend_Http_Client::POST);

        $body = trim($response->getBody());

        $location = $response->getHeader('Location');

        // if Chargify does not redirect return array of debug info
        if (empty($location)) {
            return FALSE;
        }

        /**
         * Currently Chargify does not redirect at all if your credentials are wrong.
         * It feels kind of wrong to simplly return TRUE based on that but that's the
         * best test we have right now.
         *
         * Possible other triggers:
         *
         * $body == 'Incorrect signature'
         */

        return TRUE;
    }
}