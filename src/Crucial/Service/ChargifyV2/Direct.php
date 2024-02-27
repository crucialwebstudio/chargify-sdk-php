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

namespace Crucial\Service\ChargifyV2;

use Crucial\Service\ChargifyV2;
use Crucial\Service\ChargifyV2\Direct\Utility\AuthRequest;
use Crucial\Service\ChargifyV2\Exception\BadMethodCallException;

class Direct
{
    /**
     * Instance of ChargifyV2 sent in constructor
     *
     * @var ChargifyV2
     */
    protected $service;

    /**
     * Unix timestamp
     *
     * Sent as secure[timestamp] in the request
     *
     * @var int
     */
    protected $timeStamp;

    /**
     * 40 character nonce
     *
     * Sent as secure[nonce] in the request
     *
     * @var string
     */
    protected $nonce;

    /**
     * Array of tamper-proof data to send to Chargify
     *
     * Sent as secure[data] in the request
     *
     * @var array
     */
    protected $data = array();

    /**
     * The hash_hmac() signature of the request
     *
     * Sent as secure[signature] in the request
     *
     * @var string
     */
    protected $requestSignature;

    /**
     * The URL to redirect to after submission
     *
     * @var string
     */
    protected $redirect;

    /**
     * @var AuthRequest
     */
    protected $authTestUtility;

    /**
     * Simply stores service instance
     *
     * @param ChargifyV2 $service
     */
    public function __construct(ChargifyV2 $service)
    {
        $this->service         = $service;
        $this->authTestUtility = new AuthRequest($this);
        $this->nonce           = $this->generateNonce();
        $this->timeStamp       = time();
    }

    /**
     * @return AuthRequest
     */
    public function getAuthTestUtility(): AuthRequest
    {
        return $this->authTestUtility;
    }

    /**
     * Get Crucial_Service_ChargifyV2 instance sent in constructor
     *
     * @return ChargifyV2
     */
    public function getService(): ChargifyV2
    {
        return $this->service;
    }

    /**
     * Get the API ID which is safe to expose publicly; e.g. in hidden form fields
     *
     * @return string
     */
    public function getApiId(): string
    {
        return $this->getService()->getApiId();
    }

    /**
     * Tamper-proof data that you want to send to Chargify
     *
     * @param array $data
     */
    public function setData(array $data = array())
    {
        /**
         * Since we base our signature off of the secure[data] parameter it cannot
         * be changed once the signature has been generated.
         */
        if (isset($this->requestSignature)) {
            throw new BadMethodCallException('The signature for this request has already been generated.');
        }

        $this->data = $data;
        $this->mergeRedirect();
    }

    /**
     * Getter for $this->data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get string value to send in secure[data]
     *
     * This is the query string equivalent of $this->data; generated with
     * http_build_query()
     *
     * @return string
     */
    public function getDataString(): string
    {
        // percent encoded
        $string = http_build_query($this->data);
        $string = str_replace(array('%5B', '%5D'), array('[', ']'), $string);

        return $string;
    }

    /**
     * Get string value to send in secure[data] using &amp; as the arg separator
     *
     * @return string
     */
    public function getDataStringEncoded(): string
    {
        // percent encoded
        $string = http_build_query($this->data, '', '&amp;');
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
        if (isset($this->requestSignature)) {
            throw new BadMethodCallException('The signature for this request has already been generated.');
        }

        $this->redirect = $redirect;
        $this->mergeRedirect();
    }

    /**
     * The redirect_uri must be sent with secure[data], so we merge it in with
     * $this->data
     *
     * @return void
     */
    protected function mergeRedirect()
    {
        if (!empty($this->redirect)) {
            $this->data = array_merge_recursive($this->data, array('redirect_uri' => $this->redirect));
        }
    }

    /**
     * Get a 40 character string to use as a nonce
     *
     * This is the sha1 of a random string. sha1 gives us 40 characters which is
     * the length required by Chargify Direct.
     *
     * @return string
     */
    protected function generateNonce(): string
    {
        // generate a random string
        $bits   = 256;
        $bytes  = ceil($bits / 8);
        $string = '';
        for ($i = 0; $i < $bytes; $i++) {
            $string .= chr(mt_rand(0, 255));
        }

        // sha1 hash
        return hash('sha1', $string);
    }

    /**
     * Get the URI where Chargify will redirect
     *
     * @return string
     */
    public function getRedirect(): string
    {
        return $this->redirect;
    }

    /**
     * Getter for $this->timeStamp
     *
     * @return int
     */
    public function getTimeStamp(): int
    {
        return $this->timeStamp;
    }

    /**
     * Getter for $this->nonce
     *
     * @return string
     */
    public function getNonce(): string
    {
        return $this->nonce;
    }

    /**
     * Calculate the hmac-sha1 signature of the request
     *
     * This will be sent as secure[signature] in the request
     *
     * @return string
     */
    public function getRequestSignature(): string
    {
        if (empty($this->requestSignature)) {
            $string = $this->getApiId()
                . $this->getTimeStamp()
                . $this->getNonce()
                . $this->getDataString();

            $this->requestSignature = hash_hmac('sha1', $string, $this->getService()->getApiSecret());
        }

        return $this->requestSignature;
    }

    /**
     * Calculate the hmac-sha1 signature of the response
     *
     * We will use this calculation to compare against the signature we receive
     * back from Chargify after the redirect.
     *
     * @param string $apiId      From $_GET['api_id']
     * @param string $timestamp  From $_GET['timestamp']
     * @param string $nonce      From $_GET['nonce']
     * @param string $statusCode From $_GET['status_code']
     * @param string $resultCode From $_GET['result_code']
     * @param string $callId     From $_GET['call_id']
     *
     * @return string
     * @see isValidResponseSignature()
     */
    public function getResponseSignature($apiId, $timestamp, $nonce, $statusCode, $resultCode, $callId): string
    {
        $string = $apiId
            . $timestamp
            . $nonce
            . $statusCode
            . $resultCode
            . $callId;

        return hash_hmac('sha1', $string, $this->getService()->getApiSecret());
    }

    /**
     * Test if response signature matches what we expect
     *
     * This should be called after the redirect from Chargify to verify the
     * response signature. The signature to compare against will be available in
     * the query string $_GET['signature']
     *
     * @param string $signature  Signature to compare against, from $_GET['signature']
     * @param string $apiId      From $_GET['api_id']
     * @param string $timestamp  From $_GET['timestamp']
     * @param string $nonce      From $_GET['nonce']
     * @param string $statusCode From $_GET['status_code']
     * @param string $resultCode From $_GET['result_code']
     * @param string $callId     From $_GET['call_id']
     *
     * @return bool
     * @see getResponseSignature()
     */
    public function isValidResponseSignature($signature, $apiId, $timestamp, $nonce, $statusCode, $resultCode, $callId): bool
    {
        return ($signature == $this->getResponseSignature($apiId, $timestamp, $nonce, $statusCode, $resultCode, $callId));
    }

    /**
     * The <form action=""> to use for signups
     *
     * @return string
     */
    public function getSignupAction(): string
    {
        return trim($this->getService()->getBaseUrl(), '/') . '/signups';
    }

    /**
     * The <form action=""> to use for credit card updates
     *
     * @param int $subscriptionId The ID of the subscription you want to update
     *
     * @return string
     */
    public function getCardUpdateAction($subscriptionId): string
    {
        return trim($this->getService()->getBaseUrl(), '/') . '/subscriptions/' . (string)$subscriptionId . '/card_update';
    }

    /**
     * Get the html for all hidden fields
     *
     * @return string
     */
    public function getHiddenFields(): string
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
     * @return bool
     */
    public function checkAuth(): bool
    {
        // set a fake redirect URL. Chargify will 500 on us if we don't have a redirect URL
        $this->setRedirect('http://localhost');

        return $this->authTestUtility->test();
    }
}
