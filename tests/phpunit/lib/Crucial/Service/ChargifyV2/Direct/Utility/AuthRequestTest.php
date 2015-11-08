<?php

use GuzzleHttp\Subscriber\Mock,
    Crucial\Service\ChargifyV2,
    Crucial\Service\ChargifyV2\Direct\Utility\AuthRequest;

class Crucial_Service_ChargifyV2_Direct_Utility_AuthRequestTest extends PHPUnit_Framework_TestCase
{
    public function testAuthRequestSuccess()
    {
        $mockFile = 'v2.authTest.success';
        $chargify = ClientV2Helper::getInstance();
        $direct   = $chargify->direct();

        // set a fake redirect URL. Chargify will 500 on us if we don't have a redirect URL
        $direct->setRedirect('http://localhost');

        $utilityAuthRequest = new AuthRequest($direct);
        // set a mock response on the client
        $mock = new Mock([
            MockResponse::read($mockFile)
        ]);
        $utilityAuthRequest->getHttpClient()->getEmitter()->attach($mock);

        $success  = $utilityAuthRequest->test();
        $response = $utilityAuthRequest->getLastResponse();

        // test should succeed
        $this->assertTrue($success);

        // chargify should redirect us
        $locationHeader = trim($response->getHeader('Location'));
        $this->assertTrue(!empty($locationHeader));

        // status code should be 302
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testAuthRequestFailure()
    {
        $mockFile = 'v2.authTest.error';

        // create a client with invalid credentials
        $chargify = new ChargifyV2([
            'api_id'       => 'fdsafdsaf',
            'api_password' => 'fgsdgfdsg',
            'api_secret'   => 'fdsfdsaf'
        ]);

        $direct = $chargify->direct();
        // set a fake redirect URL. Chargify will 500 on us if we don't have a redirect URL
        $direct->setRedirect('http://localhost');

        $utilityAuthRequest = new AuthRequest($direct);

        // set a mock response on the client
        $mock = new Mock([
            MockResponse::read($mockFile)
        ]);
        $utilityAuthRequest->getHttpClient()->getEmitter()->attach($mock);

        $success  = $utilityAuthRequest->test();
        $response = $utilityAuthRequest->getLastResponse();

        // test should have failed
        $this->assertFalse($success);

        // status code should be 200
        $this->assertEquals(200, $response->getStatusCode());

        // chargify should not redirect us
        $locationHeader = trim($response->getHeader('Location'));
        $this->assertTrue(empty($locationHeader));

        // body should contain 'Incorrect signature'
        $bodyIsInvalid = (0 === strcasecmp('Incorrect signature', trim((string)$response->getBody())));
        $this->assertTrue($bodyIsInvalid);
    }
}