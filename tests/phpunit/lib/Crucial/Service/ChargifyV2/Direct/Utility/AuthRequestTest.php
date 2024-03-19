<?php
namespace Test\Crucial\Service\ChargifyV2\Direct\Utility;

use PHPUnit\Framework\TestCase;
use Test\Helpers\ClientV2Helper;
use Test\Helpers\MockResponse;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;
use Crucial\Service\ChargifyV2;
use Crucial\Service\ChargifyV2\Direct\Utility\AuthRequest;

class AuthRequestTest extends TestCase
{
    public function testAuthRequestSuccess()
    {
        $chargify = ClientV2Helper::getInstance('v2.authTest.success');
        $direct   = $chargify->direct();

        // set a fake redirect URL. Chargify will 500 on us if we don't have a redirect URL
        $direct->setRedirect('http://localhost');

        $utilityAuthRequest = new AuthRequest($direct);
        $success            = $utilityAuthRequest->test();
        $response           = $utilityAuthRequest->getLastResponse();

        // test should succeed
        $this->assertTrue($success);

        // chargify should redirect us
        $locationHeader = $response->getHeader('Location');
        $this->assertTrue(!empty($locationHeader));

        // status code should be 302
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testAuthRequestFailure()
    {
        // create a client with invalid credentials
        $chargify = new ChargifyV2([
            'api_id'       => 'fdsafdsaf',
            'api_password' => 'fgsdgfdsg',
            'api_secret'   => 'fdsfdsaf'
        ]);

        $direct = $chargify->direct();
        // set a fake redirect URL. Chargify will 500 on us if we don't have a redirect URL
        $direct->setRedirect('http://localhost');

        // set mock response
        $mock    = new MockHandler([
            Psr7\Message::parseResponse(MockResponse::read('v2.authTest.error'))
        ]);
        $handler = HandlerStack::create($mock);
        $chargify->getHttpClient()->getConfig('handler')->setHandler($handler);

        $utilityAuthRequest = new AuthRequest($direct);
        $success            = $utilityAuthRequest->test();
        $response           = $utilityAuthRequest->getLastResponse();

        // test should have failed
        $this->assertFalse($success);

        // status code should be 200
        $this->assertEquals(200, $response->getStatusCode());

        // chargify should not redirect us
        $locationHeader = $response->getHeader('Location');
        $this->assertTrue(empty($locationHeader));

        // body should contain 'Incorrect signature'
        $bodyIsInvalid = (0 === strcasecmp('Incorrect signature', trim((string)$response->getBody())));
        $this->assertTrue($bodyIsInvalid);
    }
}