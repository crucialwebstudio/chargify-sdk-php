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

class Customer extends AbstractEntity
{
    /**
     * (Required)
     *
     * @param string $firstName
     *
     * @return Customer
     */
    public function setFirstName($firstName): Customer
    {
        $this->setParam('first_name', $firstName);

        return $this;
    }

    /**
     * (Required)
     *
     * @param string $lastName
     *
     * @return Customer
     */
    public function setLastName($lastName): Customer
    {
        $this->setParam('last_name', $lastName);

        return $this;
    }

    /**
     * (Required)
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email): Customer
    {
        $this->setParam('email', $email);

        return $this;
    }

    /**
     * (Optional) Company/Organization name
     *
     * @param string $organization
     *
     * @return Customer
     */
    public function setOrganization($organization): Customer
    {
        $this->setParam('organization', $organization);

        return $this;
    }

    /**
     * (Optional) Phone
     *
     * @param string $phone
     *
     * @return Customer
     */
    public function setPhone($phone): Customer
    {
        $this->setParam('phone', $phone);

        return $this;
    }

    /**
     * (Optional) Address
     *
     * @param string $address
     *
     * @return Customer
     */
    public function setAddress($address): Customer
    {
        $this->setParam('address', $address);

        return $this;
    }

    /**
     * (Optional) Address2
     *
     * @param string $address
     *
     * @return Customer
     */
    public function setAddress2($address): Customer
    {
        $this->setParam('address_2', $address);

        return $this;
    }

    /**
     * (Optional) Country
     *
     * @param string $country
     *
     * @return Customer
     */
    public function setCountry($country): Customer
    {
        $this->setParam('country', $country);

        return $this;
    }

    /**
     * (Optional) State
     *
     * @param string $state
     *
     * @return Customer
     */
    public function setState($state): Customer
    {
        $this->setParam('state', $state);

        return $this;
    }

    /**
     * (Optional) City
     *
     * @param string $city
     *
     * @return Customer
     */
    public function setCity($city): Customer
    {
        $this->setParam('city', $city);

        return $this;
    }

    /**
     * (Optional) Zip
     *
     * @param string $zip
     *
     * @return Customer
     */
    public function setZip($zip): Customer
    {
        $this->setParam('zip', $zip);

        return $this;
    }

    /**
     * (Optional, but encouraged) The unique identifier used within your own
     * application for this customer
     *
     * @param string|int $reference
     *
     * @return Customer
     */
    public function setReference($reference): Customer
    {
        $this->setParam('reference', $reference);

        return $this;
    }

    /**
     * The 'page' parameter. Used when listing customers since you can only get 50
     * at a time.
     *
     * @param int $page
     *
     * @return Customer
     */
    public function setPage($page): Customer
    {
        $this->setParam('page', $page);

        return $this;
    }

    /**
     * Create a new customer
     *
     * @return Customer
     *
     * @see Customer::setFirstName()
     * @see Customer::setLastName()
     * @see Customer::setEmail()
     * @see Customer::setOrganization()
     * @see Customer::setPhone()
     * @see Customer::setAddress()
     * @see Customer::setAddress2()
     * @see Customer::setCity()
     * @see Customer::setState()
     * @see Customer::setZip()
     * @see Customer::setCountry()
     * @see Customer::setReference()
     */
    public function create(): Customer
    {
        $service       = $this->getService();
        $rawData       = $this->getRawData(array('customer' => $this->getParams()));
        $response      = $service->request('customers', 'POST', $rawData);
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['customer'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * List all customers for a site
     *
     * @return Customer
     * @see Customer::setPage()
     */
    public function listCustomers(): Customer
    {
        $service = $this->getService();

        $response      = $service->request('customers', 'GET', null, $this->getParams());
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $this->_normalizeResponseArray($responseArray);
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Read the customer data for the given Chargify ID
     *
     * @param int $id
     *
     * @return Customer
     */
    public function readByChargifyId($id): Customer
    {
        $service = $this->getService();

        $response      = $service->request('customers/' . $id, 'GET');
        $responseArray = $this->getResponseArray($response);

        // a 404 will be returned if not found, so make sure we have a 200
        if (!$this->isError() && '200' == $response->getStatusCode()) {
            $this->_data = $responseArray['customer'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Read the customer data for the given reference (from your app)
     *
     * @return Customer
     * @see Customer::setReference()
     */
    public function readByReference(): Customer
    {
        $service = $this->getService();

        $response      = $service->request('customers/lookup', 'GET', '', $this->getParams());
        $responseArray = $this->getResponseArray($response);

        // a 404 will be returned if not found, so make sure we have a 200
        if (!$this->isError() && '200' == $response->getStatusCode()) {
            $this->_data = $responseArray['customer'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Update the customer record in Chargify.
     *
     * @param int $id
     *
     * @return Customer
     * @see Customer::setFirstName()
     * @see Customer::setLastName()
     * @see Customer::setEmail()
     * @see Customer::setOrganization()
     * @see Customer::setPhone()
     * @see Customer::setAddress()
     * @see Customer::setAddress2()
     * @see Customer::setCity()
     * @see Customer::setState()
     * @see Customer::setZip()
     * @see Customer::setCountry()
     * @see Customer::setReference()
     */
    public function update($id): Customer
    {
        $service = $this->getService();

        $rawData       = $this->getRawData(array('customer' => $this->getParams()));
        $response      = $service->request('customers/' . (int)$id, 'PUT', $rawData);
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['customer'];
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


        foreach ($responseArray as $prod) {
            $return[] = $prod['customer'];
        }

        return $return;
    }
}
