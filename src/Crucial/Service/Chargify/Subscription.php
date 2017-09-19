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

class Subscription extends AbstractEntity
{
    /**
     * The API Handle of the product for which you are creating a subscription
     * or migrating to. Required, unless a product_id is given instead.
     *
     * @param string $handle
     *
     * @return Subscription
     */
    public function setProductHandle($handle)
    {
        $this->setParam('product_handle', $handle);

        return $this;
    }

    /**
     * The ID of the Product that the subscription will migrate to.
     *
     * @param string|int $id
     *
     * @return Subscription
     */
    public function setProductId($id)
    {
        $this->setParam('product_id', $id);

        return $this;
    }

    /**
     * The ID of an existing customer within Chargify. Required, unless a
     * customer_reference or a set of customer_attributes is given.
     *
     * @param string|int $idFromChargify
     *
     * @return Subscription
     */
    public function setCustomerId($idFromChargify)
    {
        $this->setParam('customer_id', $idFromChargify);

        return $this;
    }

    /**
     * The reference value (provided by your app) of an existing customer within
     * Chargify. Required, unless a customer_id or a set of customer_attributes
     * is given.
     *
     * @param string|int $idFromYourApp
     *
     * @return Subscription
     */
    public function setCustomerReference($idFromYourApp)
    {
        $this->setParam('customer_reference', $idFromYourApp);

        return $this;
    }

    /**
     * Array containing component IDs and quantities for components to be added
     * at subscription creation time. Array should look like this:
     *
     * array(
     *  array('component_id' => $id, 'allocated_quantity' => $quantity),
     *  array('component_id' => $id, 'allocated_quantity' => $quantity),
     * )
     *
     * @param array $components
     *
     * @return Subscription
     * @todo Unit test this
     */
    public function setComponents($components)
    {
        $this->setParam('components', $components);

        return $this;
    }

    /**
     * Set the attributes for the customer. Useful when creating customer and
     * subscription at the same time.
     *
     * Possible array keys:
     *
     * first_name
     * The first name of the customer. Required when creating a customer via
     * attributes.
     *
     * last_name
     * The last name of the customer. Required when creating a customer via
     * attributes.
     *
     * email
     * The email address of the customer. Required when creating a customer via
     * attributes.
     *
     * organization
     * The organization/company of the customer. Optional.
     *
     * reference
     * A customer "reference", or unique identifier from your app, stored in
     * Chargify. Can be used so that you may reference your customers within
     * Chargify using the same unique value you use in your application. Optional.
     *
     * @param array $attributes
     *
     * @return Subscription
     */
    public function setCustomerAttributes($attributes)
    {
        $this->setParam('customer_attributes', $attributes);

        return $this;
    }

    /**
     * Set payment profile attributes. Useful when accepting (or requiring) a
     * credit card at signup.
     *
     * Possible array keys:
     *
     * first_name
     * (Optional) First name on card. If omitted, the first_name from customer
     * attributes will be used.
     *
     * last_name
     * (Optional) Last name on card. If omitted, the last_name from customer
     * attributes will be used.
     *
     * full_number
     * The full credit card number (string representation, i.e.
     * "5424000000000015")
     *
     * expiration_month
     * (Optional when performing a Subscription Import via `vault_token`,
     * required otherwise) The 1- or 2-digit credit card expiration month, as an
     * integer or string, i.e. "5"
     *
     * expiration_year
     * (Optional when performing a Subscription Import via `vault_token`,
     * required otherwise) The 4-digit credit card expiration year, as an integer
     * or string, i.e. "2012"
     *
     * cvv
     * (Optional, may be required by your gateway settings) The 3- or 4-digit
     * Card Verification Value. This value is merely passed through to the
     * payment gateway.
     *
     * billing_address
     * (Optional, may be required by your product configuration or gateway
     * settings) The credit card billing street address (i.e. “123 Main St.”).
     * This value is merely passed through to the payment gateway.
     *
     * billing_city
     * (Optional, may be required by your product configuration or gateway
     * settings) The credit card billing address city (i.e. “Boston”). This
     * value is merely passed through to the payment gateway.
     *
     * billing_state
     * (Optional, may be required by your product configuration or gateway
     * settings) The credit card billing address state (i.e. “MA”). This value is
     * merely passed through to the payment gateway.
     *
     * billing_zip
     * (Optional, may be required by your product configuration or gateway
     * settings) The credit card billing address zip code (i.e. “12345”). This
     * value is merely passed through to the payment gateway.
     *
     * billing_country
     * (Optional, may be required by your product configuration or gateway
     * settings) The credit card billing address country, preferably in
     * [ISO 3166-1 alpha-2](http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2)
     * format (i.e. “US”). This value is merely passed through to the payment
     * gateway. Some gateways require country codes in a specific format. Please
     * check your gateway’s documentation.
     *
     * vault_token
     * (Optional, used only for Subscription Import) The “token” provided by your
     * vault storage for an already stored payment profile
     *
     * customer_vault_token
     * (Optional, used only for Subscription Import) (only for Authorize.Net CIM
     * storage) The customerProfileId for the owner of the
     * customerPaymentProfileId provided as the vault_token
     *
     * current_vault
     * (Optional, used only for Subscription Import) The vault that stores the
     * payment profile with the provided vault_token. May be authorizenet,
     * trust_commerce, payment_express, beanstream, or braintree1
     *
     * last_four
     * (Optional, used only for Subscription Import) If you have the last 4
     * digits of the credit card number, you may supply them here so that we may
     * create a masked card number (i.e. ‘XXXX-XXXX-XXXX-1234’) for display in
     * the UI
     *
     * card_type
     * (Optional, used only for Subscription Import) If you know the card type
     * (i.e. Visa, MC, etc) you may supply it here so that we may display the
     * card type in the UI. May be visa, master, discover, american_express,
     * diners_club, jcb, switch, solo, dankort, maestro, forbrugsforeningen, orlaser
     *
     * @param array $attributes
     *
     * @return Subscription
     */
    public function setPaymentProfileAttributes($attributes)
    {
        $this->setParam('payment_profile_attributes', $attributes);

        return $this;
    }

    /**
     * (Optional) Can be used when canceling a subscription (via the HTTP DELETE
     * method) to make a note about the reason for cancellation.
     *
     * @param string $message
     *
     * @return Subscription
     */
    public function setCancellationMessage($message)
    {
        $this->setParam('cancellation_message', $message);

        return $this;
    }

    /**
     * (Required when using cancelDelayed() ) Set the subscription to be cancelled at the end of the current
     * billing period.
     *
     * @param bool $bool
     *
     * @return Subscription
     */
    public function setCancelAtEndOfPeriod($bool)
    {
        $this->setParam('cancel_at_end_of_period', intval($bool));

        return $this;
    }

    /**
     * (Optional, used for Subscription Import) Set this attribute to a future
     * date/time to sync imported subscriptions to your existing renewal
     * schedule. See the notes on "Date/Time Format" below. If you provide a
     * next_billing_at timestamp that is in the future, no trial or initial
     * charges will be applied when you create the subscription. In fact, no
     * payment will be captured at all. The first payment will be captured,
     * according to the prices defined by the product, near the time specified
     * by next_billing_at. If you do not provide a value for next_billing_at,
     * any trial and/or initial charges will be assessed and charged at the time
     * of subscription creation. If the card cannot be successfully charged, the
     * subscription will not be created.
     *
     * @param string $nextBilling
     *
     * @return Subscription
     *
     * @link https://docs.chargify.com/api-subscriptions#date-format
     */
    public function setNextbillingAt($nextBilling)
    {
        $this->setParam('next_billing_at', $nextBilling);

        return $this;
    }

    /**
     * Boolean, default 0. If 1 is sent initial charges will be assessed. If 0 is
     *   sent initial charges will be ignored.
     *
     * @param int $initialCharge
     *
     * @return Subscription
     */
    public function setIncludeInitialCharge($initialCharge = 0)
    {
        $this->setParam('include_initial_charge', $initialCharge);

        return $this;
    }

    /**
     * Boolean, default 0. If 1 is sent the customer will migrate to the new product with a
     *   trial if one is available. If 0 is sent, the trial period will be ignored.
     *
     * @param int $includeTrial
     *
     * @return Subscription
     */
    public function setIncludeTrial($includeTrial = 0)
    {
        $this->setParam('include_trial', $includeTrial);

        return $this;
    }

    /**
     * Enter description here...
     *
     * @param string $coupon_code
     *
     * @return Subscription
     * @link http://docs.chargify.com/api-coupons
     * @todo Unit test this
     */
    public function setCouponCode($coupon_code)
    {
        $this->setParam('coupon_code', $coupon_code);

        return $this;
    }

    /**
     * An integer value which specifies which page of results to fetch, starting
     * at 1. Fetching successively higher page numbers will return additional
     * results, until there are no more results to return (in which case an empty
     * result set will be returned). Defaults to 1.
     *
     * @param int $page
     *
     * @return Subscription
     */
    public function setPage($page)
    {
        $this->setParam('page', $page);

        return $this;
    }

    /**
     * How many records to fetch in each request, defaults to 2000. Note:
     * fetching subscriptions is currently unoptimized, so fetching large batches
     * of subscriptions will be a slow operation.
     *
     * @param int $perPage
     *
     * @return Subscription
     */
    public function setPerPage($perPage)
    {
        $this->setParam('per_page', $perPage);

        return $this;
    }

    /**
     * Get subscription data for given subscription ID
     *
     * @param int $subscriptionId
     *
     * @return Subscription
     */
    public function read($subscriptionId)
    {
        $service = $this->getService();
        $response = $service->request('subscriptions/' . (int)$subscriptionId, 'GET');
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['subscription'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Create a new subscription.
     *
     * @return Subscription
     * @see  Subscription::setProductHandle()
     * @see  Subscription::setProductId()
     * @see  Subscription::setCustomerId()
     * @see  Subscription::setCustomerAttributes()
     * @see  Subscription::setPaymentProfileAttributes()
     * @see  Subscription::setCouponCode()
     * @see  Subscription::setCustomerReference()
     * @see  Subscription::setNextBillingAt()
     * @link http://docs.chargify.com/api-subscriptions
     * @link http://docs.chargify.com/api-coupons
     */
    public function create()
    {
        $service = $this->getService();
        $rawData = $this->getRawData(array('subscription' => $this->_params));
        $response = $service->request('subscriptions', 'POST', $rawData);
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['subscription'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Set the given subscription ID to be canceled immediately
     *
     * @param int $id
     *
     * @return Subscription
     * @see Subscription::setCancellationMessage()
     */
    public function cancelImmediately($id)
    {
        $service = $this->getService();
        $rawData = !empty($this->_params)
            ? $this->getRawData(array('subscription' => $this->_params))
            : null;

        $response = $service->request('subscriptions/' . (int)$id, 'DELETE', $rawData);
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['subscription'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Set the subscription to be canceled at the end of the current billing period.
     *
     * @param int $id
     *
     * @return Subscription
     * @see Subscription::setCancelAtEndOfPeriod()
     * @see Subscription::setCancellationMessage()
     */
    public function cancelDelayed($id)
    {
        $this->setCancelAtEndOfPeriod(true);

        $service = $this->getService();
        $rawData = $this->getRawData(array('subscription' => $this->_params));
        $response = $service->request('subscriptions/' . (int)$id, 'PUT', $rawData);
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['subscription'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Reactivate the given subscription ID
     *
     * @param int $id
     *
     * @return Subscription
     * @see Subscription::setIncludeTrial()
     */
    public function reactivate($id)
    {
        $service = $this->getService();

        // this PUT request accepts a query string of 'include_trial'
        $params = array();
        $includeTrial = $this->getParam('include_trial');
        if (is_int($includeTrial)) {
            $params['include_trial'] = $includeTrial;
        }

        $response = $service->request('subscriptions/' . (int)$id . '/reactivate', 'PUT', '', $params);
        $responseArray = $this->getResponseArray($response);

        $code = $response->getStatusCode();

        /**
         * add some more errors for a bad response code. errors will be in the body of the response
         *
         * @todo Plead with Chargify for more consistent error throwing. Sometimes errors
         * are keyed by 'errors' in the response body. Sometimes (as in this case) you just get a
         * zero indexed array. Would be nice if keying was consistent and if errors had unique codes.
         */
        if ('200' != $code) {
            $this->_errors[] = $responseArray;
        }

        if (!$this->isError()) {
            $this->_data = $responseArray['subscription'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Chargify offers the ability to easily reset the balance of a subscription
     * to zero. If a subscription has a positive balance, this API call will
     * issue a credit to the subscription for the outstanding balance. This
     * is particularly helpful if you want to reactivate a canceled subscription
     * without charging the customer for their previously owed balance.
     *
     * @param int $subscriptionId
     *
     * @return Subscription
     */
    public function resetBalance($subscriptionId)
    {
        $service = $this->getService();
        $response = $service->request('subscriptions/' . (int)$subscriptionId . '/reset_balance', 'GET');
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['subscription'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Update the given subscription ID
     *
     * @param int $id
     *
     * @return Subscription
     * @see Subscription::setProductHandle()
     * @see Subscription::setCustomerAttributes()
     * @see Subscription::setPaymentProfileAttributes()
     */
    public function update($id)
    {
        $service = $this->getService();
        $rawData = $this->getRawData(array('subscription' => $this->_params));
        $response = $service->request('subscriptions/' . (int)$id, 'PUT', $rawData);
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['subscription'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * The Chargify API allows you initiate an upgrade/downgrade by posting JSON that includes:
     * product_id or
     * product_handle
     *   The ID or handle of the Product that the subscription will migrate to
     * include_trial
     *   Boolean, default 0. If 1 is sent the customer will migrate to the new product with a
     *   trial if one is available. If 0 is sent, the trial period will be ignored.
     * include_initial_charge
     *   Boolean, default 0. If 1 is sent initial charges will be assessed. If 0 is
     *   sent initial charges will be ignored.
     *
     * @param int $subscriptionId Existing subscription ID that you want to migrate
     *
     * @return Subscription
     * @see  Subscription::setProductId()
     * @see  Subscription::setProductHandle()
     * @see  Subscription::setIncludeTrial()
     * @see  Subscription::setIncludeInitialCharge()
     * @link http://docs.chargify.com/api-migrations
     * @todo ?? should this be moved to Crucial_Service_Chargify_Migration
     */
    public function migrate($subscriptionId)
    {
        $service = $this->getService();
        $rawData = $this->getRawData(array('migration' => $this->_params));
        $response = $service->request('subscriptions/' . (int)$subscriptionId . '/migrations', 'POST', $rawData);
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray['subscription'];
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * Listing subscriptions is paginated, 2000 at a time, by default. They are
     * listed most recently created first. You may control pagination using the
     * "page" and/or "per_page" parameters.
     *
     * @return Subscription
     * @see Subscription::setPage()
     * @see Subscription::setPerPage()
     */
    public function listSubscriptions()
    {
        $service = $this->getService();
        $response = $service->request('subscriptions', 'GET', NULL, $this->_params);
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $this->_normalizeResponseArray($responseArray);
        } else {
            $this->_data = array();
        }

        return $this;
    }

    /**
     * List subscriptions by customer ID
     *
     * @param int $customerId
     *
     * @return Subscription
     * @see Subscription::setCustomerId()
     */
    public function listByCustomer($customerId)
    {
        $service = $this->getService();
        $response = $service->request('customers/' . (int)$customerId . '/subscriptions', 'GET');
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
        foreach ($responseArray as $prod) {
            $return[] = $prod['subscription'];
        }

        return $return;
    }
}