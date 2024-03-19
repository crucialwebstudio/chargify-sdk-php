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

class Adjustment extends AbstractEntity
{
    /**
     * (either 'amount' or 'amount_in_cents' is required) If you use this
     * parameter, you should pass a dollar amount represented as a string. For
     * example, $10.00 would be represented as 10.00 and -$10.00 would be
     * represented as -10.00.
     *
     * @param string $amount
     *
     * @return Adjustment
     */
    public function setAmount($amount): Adjustment
    {
        $this->setParam('amount', $amount);

        return $this;
    }

    /**
     * (either 'amount' or 'amount_in_cents' is required) If you use this
     * parameter, you should pass the amount represented as a number of cents,
     * either as a string or integer. For example, $10.00 would be represented
     * as 1000 and -$10.00 would be represented as -1000. If you pass a value
     * for both 'amount' and 'amount_in_cents', the value in 'amount_in_cents'
     * will be used and 'amount' will be discarded.
     *
     * @param int $amountInCents
     *
     * @return Adjustment
     */
    public function setAmountInCents($amountInCents): Adjustment
    {
        $this->setParam('amount_in_cents', $amountInCents);

        return $this;
    }

    /**
     * A helpful explanation for the adjustment. This amount will remind you and
     * your customer for the reason for the assessment of the adjustment.
     *
     * @param string $memo
     *
     * @return Adjustment
     */
    public function setMemo($memo): Adjustment
    {
        $this->setParam('memo', $memo);

        return $this;
    }

    /**
     * (Optional) A string that toggles how the adjustment should be applied. If
     * `target` is passed for this param, the adjustment will automatically set the
     * subscription's balance to the amount. If left blank, the amount will be
     * added to the current balance.
     *
     * @param string $method
     *
     * @return Adjustment
     */
    public function setAdjustmentMethod($method): Adjustment
    {
        $this->setParam('adjustment_method', $method);

        return $this;
    }

    /**
     * Adjustments allow you to change the current balance of a subscription.
     * Adjustments with positive amounts make the balance go up, Adjustments with
     * negative amounts make the balance go down (like Credits).
     *
     * @param int $subscriptionId
     *
     * @return Adjustment
     * @see Adjustment::setAmount()
     * @see Adjustment::setAmountInCents()
     * @see Adjustment::setMemo()
     * @see Adjustment::setAdjustmentMethod()
     *
     */
    public function create($subscriptionId): Adjustment
    {
        $service       = $this->getService();
        $rawData       = $this->getRawData(array('adjustment' => $this->getParams()));
        $response      = $service->request('subscriptions/' . (int)$subscriptionId . '/adjustments', 'POST', $rawData);
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError() && '201' == $response->getStatusCode()) {
            $this->_data = $responseArray['adjustment'];
        } else {
            $this->_data = array();
        }

        return $this;
    }
}
