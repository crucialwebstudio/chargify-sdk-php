<?php

namespace Test\Crucial\Service\Chargify;

use PHPUnit\Framework\TestCase;
use Test\Helpers\ClientHelper;

/**
 * Class ComponentTest
 *
 */
class ComponentTest extends TestCase
{
    public function testCreateMeteredStairstepSuccess()
    {
        $chargify  = ClientHelper::getInstance('component.createMeteredStairstep.success');
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

        $response = $component->getService()->getLastResponse();

        // check there wasn't an error
        $this->assertFalse($component->isError(), '$component has an error');
        $this->assertEquals(201, $response->getStatusCode(), 'Expected status code 201');

        // check for a couple of attributes on the $adjustment object
        $this->assertNotEmpty($component['id'], '$component["id"] was empty');
        $this->assertEquals($component['name'], 'Text Messages', '$component["name"] mismatch');
    }

    public function testCreateMeteredStairstepError()
    {
        $chargify  = ClientHelper::getInstance('adjustment.createMeteredStairstep.error.no_prices');
        $component = $chargify->component()
            ->setName('Text Messages')
            ->setUnitName('message')
            ->setUnitPrice('0.0012')
            ->setPricingScheme('stairstep')
            ->createComponent(1234, 'metered_components');

        $response = $component->getService()->getLastResponse();

        // $component object should indicate an error
        $this->assertTrue($component->isError(), '$adjustment was not en error');
        $this->assertEquals(422, $response->getStatusCode(), 'Expected status code 422');

        // get errors from $adjustment
        $errors = $component->getErrors();

        // check for error messages
        $this->assertContains('At least 1 price bracket must be defined', $errors);
    }
}