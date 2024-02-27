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

namespace Crucial\Service\Chargify;

class Statement extends AbstractEntity
{
    /**
     * Enter description here...
     *
     * @param int $page
     *
     * @return Statement
     */
    public function setPage($page): Statement
    {
        $this->setParam('page', $page);

        return $this;
    }

    /**
     * Enter description here...
     *
     * @param int $subscriptionId
     *
     * @return Statement
     * @see Statement::setPage()
     */
    public function listStatements($subscriptionId): Statement
    {
        $service = $this->getService();

        // statements for a subscription
        $response = $service->request('subscriptions/' . (int)$subscriptionId . '/statements', 'GET', null, $this->getParams());

        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $this->_normalizeResponseArray($responseArray);
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Read a statemtn via Chargify Statemtent ID
     *
     * @param int $statementId
     *
     * @return Statement
     */
    public function read($statementId): Statement
    {
        $service = $this->getService();

        $response = $service->request('statements/' . (int)$statementId, 'GET');

        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['statement'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * This normalizes the array for us so we can rely on a consistent structure.
     *
     * @param array $responseArray
     *
     * @return array
     */
    protected function _normalizeResponseArray($responseArray)
    {
        $return = array();
        foreach ($responseArray as $trans) {
            $return[] = $trans['statement'];
        }

        return $return;
    }
}
