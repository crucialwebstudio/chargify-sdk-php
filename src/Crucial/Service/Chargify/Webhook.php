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

class Webhook extends AbstractEntity
{
    /**
     * The order in which the Webhooks are retured
     *
     * @param string $order newest_first|oldest_first
     *
     * @return Webhook
     */
    public function setOrder($order)
    {
        $this->setParam('order', $order);

        return $this;
    }

    /**
     * @param string $sinceDate YYYY-MM-DD
     *
     * @return Webhook
     */
    public function setSinceDate($sinceDate)
    {
        $this->setParam('since_date', $sinceDate);

        return $this;
    }

    /**
     * @param int $sinceId
     *
     * @return Webhook
     */
    public function setSinceId($sinceId)
    {
        $this->setParam('since_id', $sinceId);

        return $this;
    }

    /**
     * @param string $untilDate YYYY-MM-DD
     *
     * @return Webhook
     */
    public function setUntilDate($untilDate)
    {
        $this->setParam('until_date', $untilDate);

        return $this;
    }

    /**
     * @param int $page
     * @param int $perPage
     *
     * @return Webhook
     */
    public function setPaging($page, $perPage)
    {
        $this->setParam('page', $page);
        $this->setParam('per_page', $perPage);

        return $this;
    }

    public function listWebhooks()
    {
        $service = $this->getService();

        // webhooks for a site
        $response = $service->request('webhooks', 'GET', null, $this->getParams());

        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $this->_normalizeResponseArray($responseArray);
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Handle Chargify webhook
     * @see https://help.chargify.com/webhooks/webhooks-reference.html
     *
     * @return $this|boolean
     */
    public function handle($logfile = false)
    {
        // Get webhook from Post request
        $webhookHandler = new WebhookHandler($logfile);
        $webhook = $webhookHandler->get();
        return $this->webhookHandler($webhook);
    }

    /**
     * Generate fake Chargify webhook to test handle() method
     * @see https://help.chargify.com/webhooks/webhooks-reference.html
     *
     * @return $this|boolean
     */
    public function handleFake($logfile = false)
    {
        // Get fake webhook
        $webhookHandler = new WebhookHandler($logfile);
        $webhook = $webhookHandler->getFake();
        return $this->webhookHandler($webhook);
    }

    /**
     * Prepare webhook data
     *
     * @param bool|false $webhook
     * @return $this|bool
     */
    private function webhookHandler($webhook = false)
    {
        if ($webhook) {
            $this->setParam('event', $webhook['event']);
            $this->setParam('id', $webhook['id']);
            $this->setParam('payload', $webhook['payload']);

            return $this;
        } else {
            return false;
        }
    }

    /**
     * Webhook event
     * @see https://help.chargify.com/webhooks/webhooks-reference.html#events
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->getParam('event');
    }

    /**
     * Webhook id
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->getParam('id');
    }

    /**
     * Webhook payload
     * @see https://help.chargify.com/webhooks/webhooks-reference.html#payloads
     *
     * @return array
     */
    public function getPayload()
    {
        return $this->getParam('payload');
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
        foreach ($responseArray as $hook) {
            $return[] = $hook['webhook'];
        }

        return $return;
    }
}
