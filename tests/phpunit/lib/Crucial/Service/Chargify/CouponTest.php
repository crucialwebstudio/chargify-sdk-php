<?php

namespace Test\Crucial\Service\Chargify;

use PHPUnit\Framework\TestCase;
use Test\Helpers\ClientHelper;

/**
 * Class CouponTest
 *
 */
class CouponTest extends TestCase
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
}