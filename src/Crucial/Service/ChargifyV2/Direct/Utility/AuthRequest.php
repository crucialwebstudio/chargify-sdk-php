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

namespace Crucial\Service\ChargifyV2\Direct\Utility;

use Crucial\Service\ChargifyV2\Direct,
    GuzzleHttp\Client,
    GuzzleHttp\Exception\RequestException;

class AuthRequest
{
    /**
     * @var Direct
     */
    protected $direct;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var \GuzzleHttp\Message\Response|false
     */
    protected $lastResponse;

    /**
     * @param Direct $direct
     */
    public function __construct(Direct $direct)
    {
        $this->direct = $direct;

        $service = $direct->getService();

        $this->httpClient = new Client([
            'base_url' => $service->getBaseUrl(),
            'defaults' => [
                'timeout'         => 10,
                // do not allow redirects. just read the response
                'allow_redirects' => false,
                'auth'            => [$service->getApiId(), $service->getApiPassword()],
                'headers'         => [
                    'User-Agent' => 'chargify-sdk-php/1.0 (https://github.com/chargely/chargify-sdk-php)'
                ]
            ]
        ]);
    }

    /**
     * Test if credentials are correct
     *
     * @return bool
     */
    public function test()
    {
        $response = $this->request();

        // invalid If body contains 'Incorrect signature'
        $bodyIsInvalid = (0 === strcasecmp('Incorrect signature', trim((string)$response->getBody())));

        // invalid if chargify does not redirect us
        $locationHeader    = trim($response->getHeader('Location'));
        $locationIsInvalid = empty($locationHeader);

        // invalid if status code is 200
        $statusCodeIsInvalid = ($response->getStatusCode() == 200);

        if ($bodyIsInvalid || $locationIsInvalid || $statusCodeIsInvalid) {
            return false;
        }

        return true;
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @return false|\GuzzleHttp\Message\Response
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * @return \GuzzleHttp\Message\Response|false|null
     */
    protected function request()
    {
        try {
            $response = $this->httpClient->post($this->direct->getSignupAction(), [
                'body' => [
                    'secure' => [
                        'api_id'    => $this->direct->getApiId(),
                        'timestamp' => $this->direct->getTimeStamp(),
                        'nonce'     => $this->direct->getNonce(),
                        'signature' => $this->direct->getRequestSignature(),
                        'data'      => $this->direct->getDataStringEncoded()
                    ]
                ]
            ]);
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
}