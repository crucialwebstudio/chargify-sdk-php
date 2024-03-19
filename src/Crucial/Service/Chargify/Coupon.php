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

class Coupon extends AbstractEntity
{
    /**
     * The coupon code to search for when using find()
     *
     * @param string $code
     *
     * @return Coupon
     */
    public function setCode($code): Coupon
    {
        $this->setParam('code', $code);

        return $this;
    }

    /**
     * You can retrieve a coupon via the API with the show method. Retrieving a
     * coupon via the API will allow you to determine whether or not the coupon
     * is valid.
     *
     * @param int $productFamilyId
     * @param int $couponId
     *
     * @return Coupon
     */
    public function show($productFamilyId, $couponId): Coupon
    {
        $service = $this->getService();

        $response      = $service->request('product_families/' . $productFamilyId . '/coupons/' . $couponId, 'GET');
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['coupon'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * You can search for a coupon via the API with the find method. By passing a
     * code parameter, the find record will attempt to locate a coupon that
     * matches that code. This method is useful for validating coupon codes that
     * are entered by a customer. If no coupon is found, a 404 is returned.
     *
     * @param int $productFamilyId
     *
     * @return Coupon
     * @see  Coupon::setCode()
     * @todo Unit test should return empty array if coupon is not found (404)
     */
    public function find($productFamilyId): Coupon
    {
        $service       = $this->getService();
        $response      = $service->request('product_families/' . $productFamilyId . '/coupons/find', 'GET', null, $this->_params);
        $responseArray = $this->getResponseArray($response);

        // status code must be 200, otherwise the code in $this->setCode() was not found
        if (!$this->isError() && '200' == $response->getStatusCode()) {
            $this->_data = $responseArray['coupon'];
        } else {
            $this->_data = array();
        }

        return $this;
    }
}
