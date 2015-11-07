<?php
use GuzzleHttp\Subscriber\Mock;


/**
 * Class Crucial_Service_Chargify_ComponentTest
 *
 */
class Crucial_Service_Chargify_ComponentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @todo assert http response code
     */
    public function testCreateMeteredStairstepSuccess()
    {
        $chargify = ClientHelper::getInstance();

        // set a mock response on the client
        $mock = new Mock([
            MockResponse::read('component.createMeteredStairstep.success')
        ]);
        $chargify->getHttpClient()->getEmitter()->attach($mock);

        $component = $chargify->component()
            ->setName('Text Messages')
            ->setUnitName('message')
            ->setUnitPrice('0.0012')
            ->setPricingScheme('stairstep')
            ->setPrices(array(
                array(
                    'starting_quantity' => 1,
                    'ending_quantity'   => 100,
                    'unit_price'        => 1.00
                ),
                array(
                    'starting_quantity' => 101,
                    'ending_quantity'   => 1000,
                    'unit_price'        => 10.00
                )
            ))
            ->createComponent(1234, 'metered_components');

        // check there wasn't an error
        $this->assertFalse($component->isError(), '$component has an error');

        // check for a couple of attributes on the $adjustment object
        $this->assertNotEmpty($component['id'], '$component["id"] was empty');
        $this->assertEquals($component['name'], 'Text Messages', '$component["name"] mismatch');
    }

    /**
     * @todo assert http response code
     */
    public function testCreateMeteredStairstepError()
    {
        $chargify = ClientHelper::getInstance('dev');

        // set a mock response on the client
        $mock = new Mock([
            MockResponse::read('adjustment.createMeteredStairstep.error.no_prices')
        ]);
        $chargify->getHttpClient()->getEmitter()->attach($mock);

        $component = $chargify->component()
            ->setName('Text Messages')
            ->setUnitName('message')
            ->setUnitPrice('0.0012')
            ->setPricingScheme('stairstep')
            ->createComponent(1234, 'metered_components');

        // $component object should indicate an error
        $this->assertTrue($component->isError(), '$adjustment was not en error');

        // get errors from $adjustment
        $errors = $component->getErrors();

        // check for error messages
        $this->assertContains('At least 1 price bracket must be defined', $errors);
    }
}