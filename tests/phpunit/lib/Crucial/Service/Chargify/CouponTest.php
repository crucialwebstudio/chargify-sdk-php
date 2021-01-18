<?php

/**
 * Class Crucial_Service_Chargify_CouponTest
 *
 */
class Crucial_Service_Chargify_CouponTest extends PHPUnit_Framework_TestCase
{
    public function testFindCouponSuccess()
    {
        $chargify = ClientHelper::getInstance('coupon.find.success');
        $coupon   = $chargify->coupon()
            ->setCode('TEST1')
            ->find(1234);

        $response = $coupon->getService()->getLastResponse();

        // check there wasn't an error
        $this->assertFalse($coupon->isError(), '$coupon has an error');
        $this->assertEquals(200, $response->getStatusCode(), 'Expected status code 200');


        // check for a couple of attributes on the $adjustment object
        $this->assertNotEmpty($coupon['id'], '$coupon["id"] was empty');
        $this->assertEquals($coupon['code'], 'TEST1', '$coupon["code"] mismatch');
    }

    public function testFindNonExistentCodeIsError()
    {
        $chargify = ClientHelper::getInstance('coupon.find.error');
        $coupon   = $chargify->coupon()
            ->setCode('THIS_CODE_DOESNT_EXIST')
            ->find(1234);

        $response = $coupon->getService()->getLastResponse();

        // $component object should indicate an error
        $this->assertTrue($coupon->isError(), '$coupon was not en error');
        $this->assertEquals(404, $response->getStatusCode(), 'Expected status code 404');
    }

    public function testCreateSuccess()
    {
        $chargify = ClientHelper::getInstance('coupon.success');
        $coupon   = $chargify->coupon()
            ->setParam('name', 'Test user coupon')
            ->setParam('code', 'PROXIO_TESTER_0001')
            ->setParam('description', 'Coupon to avoid billing on test user.')
            ->setParam('percentage', '100')
            ->setParam('product_family_id', '123456')
            ->setParam('conversion_limit', '1')
            ->create('123456');

        $response = $coupon->getService()->getLastResponse();

        // check there wasn't an error
        $this->assertFalse($coupon->isError(), '$coupon has an error');
        $this->assertEquals(201, $response->getStatusCode(), 'Expected status code 201');

        // check for a couple of attributes on the $coupon object
        $this->assertNotEmpty($coupon['id'], '$coupon["id"] was empty');
        $this->assertEquals('1', $coupon['conversion_limit'], '$subscription["conversion_limit"] did not match what was given in request');
    }

    public function testValidateSuccess()
    {
        $chargify = ClientHelper::getInstance('coupon.validate.success');
        $coupon   = $chargify->coupon()
            ->setCode('TEST1')
            ->validate(1234, 'PROXIO_TESTER');

        $response = $coupon->getService()->getLastResponse();

        // check there wasn't an error
        $this->assertFalse($coupon->isError(), '$coupon has an error');
        $this->assertEquals(200, $response->getStatusCode(), 'Expected status code 200');

        // check for a couple of attributes on the $adjustment object
        $this->assertNotEmpty($coupon['id'], '$coupon["id"] was empty');
        $this->assertEquals($coupon['id'], '411008', '$coupon["id"] mismatch');
        $this->assertEquals($coupon['product_family_id'], '1312055', '$coupon["product_family_id"] mismatch');
        $this->assertEquals($coupon['code'], 'PROXIO_TESTER', '$coupon["code"] mismatch');
    }

    public function testValidateNonExistentCodeIsError()
    {
        $chargify = ClientHelper::getInstance('coupon.validate.error');
        $coupon   = $chargify->coupon()
            ->setCode('THIS_CODE_DOESNT_EXIST')
            ->validate(1234, 1234);

        $response = $coupon->getService()->getLastResponse();

        // $component object should indicate an error
        $this->assertTrue($coupon->isError(), '$coupon was not en error');
        $this->assertEquals(404, $response->getStatusCode(), 'Expected status code 404');
    }

    public function testArchiveSuccess()
    {
        $chargify = ClientHelper::getInstance('coupon.archive.success');
        $coupon   = $chargify->coupon()
            ->archive('411029', '1312055');

        $response = $coupon->getService()->getLastResponse();

        // check there wasn't an error
        $this->assertFalse($coupon->isError(), '$coupon has an error');
        $this->assertEquals(200, $response->getStatusCode(), 'Expected status code 200');

        // check for a couple of attributes on the $coupon object
        $this->assertNotEmpty($coupon['id'], '$coupon["id"] was empty');
        $this->assertEquals('411029', $coupon['id'], '$subscription["id"] did not match what was given in request');
        $this->assertEquals('2021-01-11T14:09:15-08:00', $coupon['archived_at'], '$subscription["archived_at"] did not match what was given in request');
    }
}
