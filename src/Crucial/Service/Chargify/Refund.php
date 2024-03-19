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

class Refund extends AbstractEntity
{
    /**
     * (required) The id of the Payment that the credit will be applied to
     *
     * @param int $paymentId
     *
     * @return Refund
     */
    public function setPaymentId($paymentId): Refund
    {
        $this->setParam('payment_id', $paymentId);

        return $this;
    }

    /**
     * (either 'amount' or 'amount_in_cents' is required) If you use this
     * parameter, you should pass a dollar amount represented as a string. For
     * example, $10.00 would be represented as 10.00 and -$10.00 would be
     * represented as -10.00.
     *
     * @param string $amount
     *
     * @return Refund
     */
    public function setAmount($amount): Refund
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
     * @return Refund
     */
    public function setAmountInCents($amountInCents): Refund
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
     * @return Refund
     */
    public function setMemo($memo): Refund
    {
        $this->setParam('memo', $memo);

        return $this;
    }

    /**
     * Creating a refund requires a valid, live subscription and payment. A
     * refund in the amount specified will be immediately applied to the
     * customer's credit card.
     *
     * NOTE: Refunds are currently only suppoerted by the Authorize.net payment
     * gateway.
     *
     * @param int $subscriptionId
     *
     * @return Refund
     * @see Refund::setAmount()
     * @see Refund::setAmountInCents()
     * @see Refund::setMemo()
     * @see Refund::setPaymentId()
     */
    public function create($subscriptionId): Refund
    {
        $service       = $this->getService();
        $rawData       = $this->getRawData(array('refund' => $this->getParams()));
        $response      = $service->request('subscriptions/' . (int)$subscriptionId . '/refunds', 'POST', $rawData);
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError() && '201' == $response->getStatusCode()) {
            $this->_data = $responseArray['refund'];
        } else {
            $this->_data = array();
        }

        return $this;
    }
}
