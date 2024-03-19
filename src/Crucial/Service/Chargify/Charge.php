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

class Charge extends AbstractEntity
{
    /**
     * (either 'amount' or 'amount_in_cents' is required) If you use this
     * parameter, you should pass a dollar amount represented as a string. For
     * example, $10.00 would be represented as 10.00.
     *
     * @param string $amount
     *
     * @return Charge
     */
    public function setAmount($amount): Charge
    {
        $this->setParam('amount', $amount);

        return $this;
    }

    /**
     * (either 'amount' or 'amount_in_cents' is required) If you use this
     * parameter, you should pass the amount represented as a number of cents,
     * either as a string or integer. For example, $10.00 would be represented
     * as 1000. If you pass a value for both 'amount' and 'amount_in_cents, the
     * value in 'amount_in_cents' will be used and 'amount' will be discarded.
     *
     * @param int $amountInCents
     *
     * @return Charge
     */
    public function setAmountInCents($amountInCents): Charge
    {
        $this->setParam('amount_in_cents', $amountInCents);

        return $this;
    }

    /**
     * (required) A helpful explanation for the charge. This amount will remind
     * you and your customer for the reason for the assessment of the charge.
     *
     * @param string $memo
     *
     * @return Charge
     */
    public function setMemo($memo): Charge
    {
        $this->setParam('memo', $memo);

        return $this;
    }

    /**
     * For "live" subscriptions (i.e. subscriptions that are not canceled or expired)
     * you have the ability to attach a one-time (or "one-off") charge of an
     * arbitrary amount.Enter description here...
     *
     * @param int $subscriptionId
     *
     * @return Charge
     * @see Charge::setAmount()
     * @see Charge::setAmountInCents()
     * @see Charge::setMemo()
     */
    public function create($subscriptionId): Charge
    {
        $service       = $this->getService();
        $rawData       = $this->getRawData(array('charge' => $this->getParams()));
        $response      = $service->request('subscriptions/' . (int)$subscriptionId . '/charges', 'POST', $rawData);
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['charge'];
        } else {
            $this->_data = array();
        }

        return $this;
    }
}
