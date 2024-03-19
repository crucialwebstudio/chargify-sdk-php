<?php
namespace Test\Crucial\Service\ChargifyV2;

use PHPUnit\Framework\TestCase;
use Test\Helpers\ClientV2Helper;

/**
 * Class CallTest
 *
 */
class CallTest extends TestCase
{
    public function testReadSuccess()
    {
        $chargify = ClientV2Helper::getInstance('v2.call.read.success');

        // set a mock response on the client
//        $mock = new MockHandler([
//            MockResponse::read('v2.call.read.success')
//        ]);
//        $chargify->getHttpClient()->getEmitter()->attach($mock);

        $call     = $chargify->call()->readByChargifyId('1234');
        $response = $call->getService()->getLastResponse();

        // check there wasn't an error
        $this->assertFalse($call->isError(), '$call has an error');
        $this->assertEquals(200, $response->getStatusCode(), 'Expected status code 200');

        // check for a couple of attributes on the $adjustment object
        $this->assertEquals(1234, $call['id'], '$call["id"] mismatch');
    }

    public function testNotFoundCreatesError()
    {
        $chargify = ClientV2Helper::getInstance('v2.call.read.error.not_found');

        // set a mock response on the client
//        $mock = new MockHandler([
//            MockResponse::read($mockFile)
//        ]);
//        $chargify->getHttpClient()->getEmitter()->attach($mock);

        $call     = $chargify->call()->readByChargifyId('1234');
        $response = $call->getService()->getLastResponse();

        // $adjustment object should indicate an error
        $this->assertTrue($call->isError(), '$call was not en error');
        $this->assertEquals(404, $response->getStatusCode(), 'Expected status code 404');

        // check for error messages
//        $errors = $call->getErrors();
//        $this->assertContains([
//            'source'    => 'client',
//            'attribute' => null,
//            'kind'      => 'status_code',
//            'message'   => 'Bad status code: 404'
//        ], $errors);
    }
}