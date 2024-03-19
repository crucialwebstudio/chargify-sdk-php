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

class Transaction extends AbstractEntity
{
    /**
     * An array of transaction types. Multiple values can be passed in the url,
     * for example: http://example.com?kinds[]=charge&kinds[]=payment&kinds[]=credit
     *
     * The following is a list of available transaction types.
     *
     * charge
     * refund
     * payment
     * credit
     * payment_authorization
     * info
     * adjustment
     *
     * @param array $kinds
     *
     * @return Transaction
     */
    public function setKinds(array $kinds): Transaction
    {
        $this->setParam('kinds', $kinds);

        return $this;
    }

    /**
     * Returns transactions with an id greater than or equal to the one specified
     *
     * @param int $sinceId
     *
     * @return Transaction
     */
    public function setSinceId(int $sinceId): Transaction
    {
        $this->setParam('since_id', $sinceId);

        return $this;
    }

    /**
     * Returns transactions with an id less than or equal to the one specified
     *
     * @param int $maxId
     *
     * @return Transaction
     */
    public function setMaxId(int $maxId): Transaction
    {
        $this->setParam('max_id', $maxId);

        return $this;
    }

    /**
     * Returns transactions with a created_at date greater than or equal to the
     * one specified
     *
     * @param string $sinceDate ; format YYYY-MM-DD
     *
     * @return Transaction
     */
    public function setSinceDate(string $sinceDate): Transaction
    {
        $this->setParam('since_date', $sinceDate);

        return $this;
    }

    /**
     * Returns transactions with a created_at date less than or equal to the one specified
     *
     * @param string $untilDate ; format YYYY-MM-DD
     *
     * @return Transaction
     */
    public function setUntilDate(string $untilDate): Transaction
    {
        $this->setParam('until_date', $untilDate);

        return $this;
    }

    /**
     * The page number and number of results used for pagination. By default
     * results are paginated 20 per page.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return Transaction
     */
    public function setPagination(int $page, int $perPage): Transaction
    {
        $this->setParam('page', $page);
        $this->setParam('per_page', $perPage);

        return $this;
    }

    /**
     * Retrieve transactions for a specific subscription
     *
     * @param int $subscriptionId ; Chargify subscription_id
     *
     * @return Transaction
     * @see Transaction:setKinds()
     * @see Transaction::setPagination()
     * @see Transaction:setSinceDate()
     * @see Transaction::setUntilDate()
     * @see Transaction::setMaxId()
     * @see Transaction::setSinceId()
     */
    public function listBySubscription(int $subscriptionId): Transaction
    {
        $service = $this->getService();

        // transactions for a subscription
        $response = $service->request('subscriptions/' . $subscriptionId . '/transactions', 'GET', null, $this->getParams());

        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $this->_normalizeResponseArray($responseArray);
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Retrieve transactions for your entire site
     *
     * @return Transaction
     * @see Transaction:setKinds()
     * @see Transaction::setPagination()
     * @see Transaction:setSinceDate()
     * @see Transaction::setUntilDate()
     * @see Transaction::setMaxId()
     * @see Transaction::setSinceId()
     */
    public function listBySite(): Transaction
    {
        $service = $this->getService();

        // transactions for a subscription
        $response = $service->request('transactions', 'GET', null, $this->getParams());

        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $this->_normalizeResponseArray($responseArray);
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
            $return[] = $trans['transaction'];
        }

        return $return;
    }
}
