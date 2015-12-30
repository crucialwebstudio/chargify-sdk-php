<?php

/**
 * Class Crucial_Service_Chargify_CustomerTest
 *
 */
class Crucial_Service_Chargify_CustomerTest extends PHPUnit_Framework_TestCase
{

    public function testReadByReference()
    {
        $chargify = ClientHelper::getInstance('customer.readByReference.success');
        $customer = $chargify->customer()
            ->setReference(123456)
            ->readByReference();

        $response = $customer->getService()->getLastResponse();

        $this->assertFalse($customer->isError(), '$customer has an error');
        $this->assertEquals(200, $response->getStatusCode(), 'Expected status code 200');
    }

    public function testReadByChargifyId()
    {
        $chargify = ClientHelper::getInstance('customer.readByChargifyId.success');
        $customer = $chargify->customer()
            ->readByChargifyId(12345);

        $response = $customer->getService()->getLastResponse();

        $this->assertFalse($customer->isError(), '$customer has an error');
        $this->assertEquals(200, $response->getStatusCode(), 'Expected status code 200');
    }
}