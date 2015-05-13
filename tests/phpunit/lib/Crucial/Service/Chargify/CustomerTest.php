<?php
use Crucial\Service\Chargify,
    GuzzleHttp\Subscriber\Mock;


/**
 * Class Crucial_Service_Chargify_CustomerTest
 *
 * @todo use Guzzle service builder for creating guzzle clients
 */
class Crucial_Service_Chargify_CustomerTest extends PHPUnit_Framework_TestCase
{

    public function testReadByReference()
    {
        $chargify = new Chargify(array(
            'hostname'   => 'fgdfsgdfsgfds',
            'api_key'    => 'hgfdhdfghd',
            'shared_key' => 'hgfdhgfdhg'
        ));

        // set a mock response on the client
        $mock = new Mock([
            MockResponse::read('customer.readByReference.success')
        ]);
        $chargify->getHttpClient()->getEmitter()->attach($mock);

        $customer = $chargify->customer()
            ->setReference(123456)
            ->readByReference();

        $this->assertFalse($customer->isError(), '$customer has an error');
    }

    public function testReadByChargifyId()
    {
        $chargify = new Chargify(array(
            'hostname'   => 'fgdfsgdfsgfds',
            'api_key'    => 'hgfdhdfghd',
            'shared_key' => 'hgfdhgfdhg'
        ));

        // set a mock response on the client
        $mock = new Mock([
            MockResponse::read('customer.readByChargifyId.success')
        ]);
        $chargify->getHttpClient()->getEmitter()->attach($mock);

        $customer = $chargify->customer()
            ->readByChargifyId(8003316);

        $this->assertFalse($customer->isError(), '$customer has an error');
    }
}