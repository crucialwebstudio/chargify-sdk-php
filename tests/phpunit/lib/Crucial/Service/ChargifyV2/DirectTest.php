<?php
use GuzzleHttp\Subscriber\Mock;


/**
 * Class Crucial_Service_ChargifyV2_DirectTest
 *
 */
class Crucial_Service_ChargifyV2_DirectTest extends PHPUnit_Framework_TestCase
{
    public function testAuthSuccess()
    {
        $mockFile = 'v2.authTest.success';
        $chargify = ClientV2Helper::getInstance();
        $direct   = $chargify->direct();

        // set mock on authtest http client before testing
        $authUtility = $direct->getAuthTestUtility();
        $mock = new Mock([
            MockResponse::read($mockFile)
        ]);
        $authUtility->getHttpClient()->getEmitter()->attach($mock);

        $success = $direct->checkAuth();

        $this->assertTrue($success);
    }

    public function testAuthFailure()
    {
        $mockFile = 'v2.authTest.error';
        $chargify = ClientV2Helper::getInstance();
        $direct   = $chargify->direct();

        // set mock on authtest http client before testing
        $authUtility = $direct->getAuthTestUtility();
        $mock = new Mock([
            MockResponse::read($mockFile)
        ]);
        $authUtility->getHttpClient()->getEmitter()->attach($mock);

        $success = $direct->checkAuth();

        $this->assertFalse($success);
    }
}