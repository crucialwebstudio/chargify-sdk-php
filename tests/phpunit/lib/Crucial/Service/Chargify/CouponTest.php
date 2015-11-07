<?php
use GuzzleHttp\Subscriber\Mock;


/**
 * Class Crucial_Service_Chargify_CouponTest
 *
 */
class Crucial_Service_Chargify_CouponTest extends PHPUnit_Framework_TestCase
{
    public function testFindCouponSuccess()
    {
        $chargify = ClientHelper::getInstance();

        // set a mock response on the client
        $mock = new Mock([
            MockResponse::read('coupon.find.success')
        ]);
        $chargify->getHttpClient()->getEmitter()->attach($mock);

        $coupon = $chargify->coupon()
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
        $chargify = ClientHelper::getInstance();

        // set a mock response on the client
        $mock = new Mock([
            MockResponse::read('coupon.find.error')
        ]);
        $chargify->getHttpClient()->getEmitter()->attach($mock);

        $coupon = $chargify->coupon()
            ->setCode('THIS_CODE_DOESNT_EXIST')
            ->find(1234);

        $response = $coupon->getService()->getLastResponse();

        // $component object should indicate an error
        $this->assertTrue($coupon->isError(), '$coupon was not en error');
        $this->assertEquals(404, $response->getStatusCode(), 'Expected status code 404');
    }
}