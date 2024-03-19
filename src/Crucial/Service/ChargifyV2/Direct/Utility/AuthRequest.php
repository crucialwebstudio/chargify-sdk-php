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

namespace Crucial\Service\ChargifyV2\Direct\Utility;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Crucial\Service\ChargifyV2\Direct;

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
     * @var Response|false
     */
    protected $lastResponse;

    /**
     * @param Direct $direct
     */
    public function __construct(Direct $direct)
    {
        $this->direct     = $direct;
        $this->httpClient = $direct->getService()->getHttpClient();
    }

    /**
     * Test if credentials are correct
     *
     * @return bool
     */
    public function test(): bool
    {
        $response = $this->request();
        $body     = trim((string)$response->getBody());

        // invalid If body contains 'Incorrect signature'
        $bodyIsInvalid = (0 === strcasecmp('Incorrect signature', $body));

        // invalid if chargify does not redirect us
        $locationHeader    = $response->getHeader('Location');
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
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    /**
     * @return Response|false
     */
    public function getLastResponse(): Response|false
    {
        return $this->lastResponse;
    }

    /**
     * @return Response|false|null
     */
    protected function request(): Response|false|null
    {
        try {
            $response = $this->httpClient->post($this->direct->getSignupAction(), [
                'form_params' => [
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
