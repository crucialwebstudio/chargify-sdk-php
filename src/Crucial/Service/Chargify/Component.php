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

class Component extends AbstractEntity
{
    /**
     * The quantity of a Quantity Based Component to assign to a subscription
     *
     * @param int $quantity
     *
     * @return Component
     */
    public function setAllocatedQuantity($quantity): Component
    {
        $this->setParam('allocated_quantity', $quantity);

        return $this;
    }

    /**
     * Create a usage for a Metered Usage Component. Note that you can also send a
     * negative number to decrease the usage.
     *
     * @param int $quantity
     *
     * @return Component
     */
    public function setUsageQuantity($quantity): Component
    {
        $this->setParam('quantity', $quantity);

        return $this;
    }

    /**
     * Set the memo for this usage or allocation.
     *
     * @param string $memo
     *
     * @return Component
     */
    public function setMemo($memo): Component
    {
        $this->setParam('memo', $memo);

        return $this;
    }

    /**
     * Set the name of the component to be created, i.e. "Text Messages"
     *
     * @param string $name
     *
     * @return Component
     */
    public function setName($name): Component
    {
        $this->setParam('name', $name);

        return $this;
    }

    /**
     * (Not required for On/Off Components) The name of the unit that the
     * component's usage is measured in. i.e. message
     *
     * @param string $name
     *
     * @return Component
     */
    public function setUnitName($name): Component
    {
        $this->setParam('unit_name', $name);

        return $this;
    }

    /**
     * The amount the customer will be charged per unit. The price can contain up
     * to 4 decimal places. i.e. $1.00, $0.0012, etc.
     *
     * @param string $price
     *
     * @return Component
     */
    public function setUnitPrice($price): Component
    {
        $this->setParam('unit_price', $price);

        return $this;
    }

    /**
     * (Not required for On/Off Components or 'per_unit' pricing schemes) One or
     * more price brackets. See Product Components for an overview of how price
     * brackets work for different pricing schemes.
     *
     * @param string $scheme ; One of the following:
     *                         - per_unit
     *                         - volume
     *                         - tiered
     *                         - stairstep
     *
     * @return Component
     * @link http://docs.chargify.com/product-components
     */
    public function setPricingScheme($scheme): Component
    {
        $this->setParam('pricing_scheme', $scheme);

        return $this;
    }

    /**
     * An array of price brackets. If the component uses the 'per_unit' pricing
     * scheme, this array will be empty. Available options:
     * - starting_quantity
     * - ending_quantity
     * - unit_price
     *
     * array(
     *  array(
     *    'starting_quantity' => 1,
     *    'ending_quantity' => 20,
     *    'unit_price' => 19.00
     *  )
     * )
     *
     * @param string $prices
     *
     * @return Component
     * @link http://docs.chargify.com/product-components
     */
    public function setPrices($prices): Component
    {
        $this->setParam('prices', $prices);

        return $this;
    }

    /**
     * List components for a subscription
     *
     * @param int $id
     *
     * @return Component
     */
    public function listSubscription($id): Component
    {
        $service = $this->getService();

        $response      = $service->request('subscriptions/' . (int)$id . '/components', 'GET');
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $this->_normalizeResponseArray($responseArray);
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Read component for a subscription
     *
     * @param int $subscriptionId
     * @param int $componentId
     *
     * @return Component
     */
    public function readSubscription($subscriptionId, $componentId): Component
    {
        $service = $this->getService();

        $response      = $service->request('subscriptions/' . (int)$subscriptionId . '/components/' . (int)$componentId, 'GET');
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['component'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Set quantity of Quantity Based Components for a subscription
     *
     * @param int $subscriptionId
     * @param int $componentId
     *
     * @return Component
     * @see Component::setAllocatedQuantity()
     */
    public function setQuantityAllocation($subscriptionId, $componentId): Component
    {
        $service       = $this->getService();
        $rawData       = $this->getRawData(array('component' => $this->_params));
        $response      = $service->request('subscriptions/' . (int)$subscriptionId . '/components/' . (int)$componentId, 'PUT', $rawData);
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['component'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Create an allocation for Quantity-Based on On/Off component
     *
     * @param int $subscriptionId
     * @param int $componentId
     *
     * @return Component
     * @see Component::setUsageQuantity()
     * @see Component::setMemo()
     */
    public function createQuantityAllocation($subscriptionId, $componentId): Component
    {
        $service       = $this->getService();
        $rawData       = $this->getRawData(array('allocation' => $this->_params));
        $response      = $service->request('subscriptions/' . (int)$subscriptionId . '/components/' . (int)$componentId . '/allocations', 'POST', $rawData);
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['allocation'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Create a usage for a Metered Usage Component
     *
     * @param int $subscriptionId
     * @param int $componentId
     *
     * @return Component
     * @see Component::setUsageQuantity()
     * @see Component::setUsageMemo()
     */
    public function createUsage($subscriptionId, $componentId): Component
    {
        $service  = $this->getService();
        $rawData  = $this->getRawData(array('usage' => $this->_params));
        $response = $service->request('subscriptions/' . (int)$subscriptionId . '/components/' . (int)$componentId . '/usages', 'POST', $rawData);

        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['usage'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * List components for a product family
     *
     * @param int $productFamilyId
     *
     * @return Component
     */
    public function listProductFamily($productFamilyId): Component
    {
        $service = $this->getService();

        $response      = $service->request('product_families/' . (int)$productFamilyId . '/components', 'GET');
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $this->_normalizeResponseArray($responseArray);
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Read a component belonging to a product family
     *
     * @param int $productFamilyId
     * @param int $componentId
     *
     * @return Component
     */
    public function readProductFamily($productFamilyId, $componentId): Component
    {
        $service = $this->getService();

        $response      = $service->request('product_families/' . (int)$productFamilyId . '/components/' . (int)$componentId, 'GET');
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['component'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Create a component
     *
     * @param int    $productFamilyId
     * @param string $componentType ; one of the following:
     *                              - metered_components
     *                              - quantity_based_components
     *                              - on_off_components
     *
     * @return Component
     * @see Component::setName()
     * @see Component::setUnitName()
     * @see Component::setUnitPrice()
     * @see Component::setPricingScheme()
     * @see Component::setPrices()
     */
    public function createComponent($productFamilyId, $componentType): Component
    {
        $service = $this->getService();

        $rawDataKey = '';
        switch ($componentType) {
            case 'metered_components':
                $rawDataKey = 'metered_component';
                break;
            case 'quantity_based_components':
                $rawDataKey = 'quantity_based_component';
                break;
            case 'on_off_components':
                $rawDataKey = 'on_off_component';
                break;
            default:
                break;
        }
        $rawData  = $this->getRawData(array($rawDataKey => $this->_params));
        $response = $service->request('product_families/' . (int)$productFamilyId . '/' . $componentType, 'POST', $rawData);

        $responseArray = $this->getResponseArray($response);

        if (!$this->isError() && '201' == $response->getStatusCode()) {
            $this->_data = $responseArray['component'];
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
    protected function _normalizeResponseArray($responseArray): array
    {
        $return = array();
        foreach ($responseArray as $prod) {
            $return[] = $prod['component'];
        }

        return $return;
    }
}
