<?php
use Crucial\Service\Chargify,
    GuzzleHttp\Subscriber\Mock;


/**
 * Class Crucial_Service_Chargify_SubscriptionTest
 *
 * @todo use Guzzle service builder for creating guzzle clients
 */
class Crucial_Service_Chargify_SubscriptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @todo assert http response code
     */
    public function testCreateSuccess()
    {
        $chargify = new Chargify(array(
            'hostname'   => 'fgdfsgdfsgfds',
            'api_key'    => 'hgfdhdfghd',
            'shared_key' => 'hgfdhgfdhg'
        ));

        // set a mock response on the client
        $mock = new Mock([
            MockResponse::read('subscription.success')
        ]);
        $chargify->getHttpClient()->getEmitter()->attach($mock);

        $subscription = $chargify->subscription()
            ->setProductId(123)
            ->setCustomerAttributes(array(
                'first_name'   => 'Darryl',
                'last_name'    => 'Strawberry',
                // don't change this email. we are making an assertion on its value below
                'email'        => 'darryl@mailinator.com',
                'organization' => 'Mets',

                // shipping address fields
                'phone'        => '555-555-1234',
                'address'      => '123 Main St',
                'address_2'    => 'Apt 123',
                'city'         => 'New York',
                'state'        => 'NY',
                'zip'          => '48433',
                'country'      => 'US',
            ))
            ->setPaymentProfileAttributes(array(
                'first_name'       => 'Darryl2',
                'last_name'        => 'Strawberry2',
                'full_number'      => '1',
                'expiration_month' => '03',
                'expiration_year'  => '16',
                'cvv'              => '123',
                'billing_address'  => '600 N',
                'billing_city'     => 'Chicago',
                'billing_state'    => 'IL',
                'billing_zip'      => '60610',
                'billing_country'  => 'US'
            ))
            ->create();

        // check there wasn't an error
        $this->assertFalse($subscription->isError(), '$subscription has an error');

        // check for a couple of attributes on the $subscription object
        $this->assertNotEmpty($subscription['id'], '$subscription["id"] was empty');
        $this->assertEquals('darryl@mailinator.com', $subscription['customer']['email'], '$subscription["customer"]["email"] did not match what was given in request');
    }


    /**
     * @todo assert http response code
     */
    public function testNoShippingCreatesError()
    {
        $chargify = new Chargify(array(
            'hostname'   => 'sdfdsf',
            'api_key'    => 'fsdfdsf',
            'shared_key' => 'fsdfdsf'
        ));

        // set a mock response on the client
        $mock = new Mock([
            MockResponse::read('subscription.error.no_shipping')
        ]);
        $chargify->getHttpClient()->getEmitter()->attach($mock);

        $subscription = $chargify->subscription()
            ->setProductId(123)
            ->setCustomerAttributes(array(
                'first_name'   => 'Darryl',
                'last_name'    => 'Strawberry',
                'email'        => 'darryl@mailinator.com',
                'organization' => 'Mets'
                /**
                 * Note the omission of shipping fields here. They are required for this product so we should get an
                 * error from the API.
                 */
            ))
            ->setPaymentProfileAttributes(array(
                'first_name'       => 'Darryl2',
                'last_name'        => 'Strawberry2',
                'full_number'      => '1',
                'expiration_month' => '03',
                'expiration_year'  => '16',
                'cvv'              => '123',
                'billing_address'  => '600 N',
                'billing_city'     => 'Chicago',
                'billing_state'    => 'IL',
                'billing_zip'      => '60610',
                'billing_country'  => 'US'
            ))
            ->create();

        // $subscription object should be in an error state
        $this->assertTrue($subscription->isError());

        // get errors from $subscription
        $errors = $subscription->getErrors();

        // check for
        $this->assertContains('Shipping Address: cannot be blank.', $errors);
    }
}