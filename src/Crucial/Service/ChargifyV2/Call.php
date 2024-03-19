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

class Call extends AbstractEntity
{
    /**
     * Read the call data for the given Chargify ID
     *
     * @param string $callId
     *
     * @return Call
     */
    public function readByChargifyId($callId): Call
    {
        $service = $this->getService();

        $response      = $service->request('calls/' . $callId, 'GET');
        $responseArray = $this->getResponseArray($response);

        // a 404 will be returned if not found, so make sure we have a 200
        if (!$this->isError() && '200' == $response->getStatusCode()) {
            $this->_data = $responseArray['call'];
        } else {
            $this->_data = $responseArray;
        }

        return $this;
    }
}
