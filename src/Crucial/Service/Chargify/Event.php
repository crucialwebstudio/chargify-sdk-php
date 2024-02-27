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

class Event extends AbstractEntity
{
    /**
     * The page number and number of results used for pagination. By default
     * results are paginated 30 per page.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return Event
     */
    public function setPagination($page, $perPage): Event
    {
        $this->setParam('page', $page);
        $this->setParam('per_page', $perPage);

        return $this;
    }

    /**
     * Set the lowermost event ID that you want returned.
     * Only events with an event ID higher than this will be returned.
     *
     * @param int $sinceId
     *
     * @return Event
     */
    public function setSinceId($sinceId): Event
    {
        $this->setParam('since_id', $sinceId);

        return $this;
    }

    /**
     * Set the uppermost event ID that you want returned.
     * Only events with an event ID lower than this will be returned.
     *
     * @param int $sinceId
     *
     * @return Event
     */
    public function setMaxId($maxId): Event
    {
        $this->setParam('max_id', $maxId);

        return $this;
    }

    /**
     * Set direction events should be returned.
     * I believe this should be 'asc' or 'desc'. It's not documented in Chargify's
     * docs.
     *
     * @param string $direction
     *
     * @return Event
     */
    public function setDirection($direction): Event
    {
        $this->setParam('direction', $direction);

        return $this;
    }

    /**
     * Return events for a site
     *
     * @return Event
     * @see Event::setPagination()
     * @see Event::setSinceId()
     * @see Event::setMaxId()
     * @see Event::setDirection()
     */
    public function forSite(): Event
    {
        $service = $this->getService();

        // events for a site
        $response = $service->request('events', 'GET', null, $this->getParams());

        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $this->_normalizeResponseArray($responseArray);
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Return events for the given subscription
     *
     * @param int $subscriptionId
     *
     * @return Event
     * @see Event::setPagination()
     * @see Event::setSinceId()
     * @see Event::setMaxId()
     * @see Event::setDirection()
     */
    public function forSubscription($subscriptionId): Event
    {
        $service = $this->getService();

        // events for a subscription
        $response = $service->request('subscriptions/' . $subscriptionId . '/events', 'GET', null, $this->getParams());

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
        foreach ($responseArray as $event) {
            $return[] = $event['event'];
        }

        return $return;
    }
}
