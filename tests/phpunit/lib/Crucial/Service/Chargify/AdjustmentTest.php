<?php

namespace Test\Crucial\Service\Chargify;

use PHPUnit\Framework\TestCase;
use Test\Helpers\ClientHelper;

/**
 * Class AdjustmentTest
 *
 */
class AdjustmentTest extends TestCase
{
    public function testCreateSuccess()
    {
        $chargify   = ClientHelper::getInstance('adjustment.create.success');
        $adjustment = $chargify->adjustment()
            ->setAmountInCents(1099)
            ->setMemo('Test Memo')
            ->setAdjustmentMethod('target')
            ->create(123);

        $response = $adjustment->getService()->getLastResponse();

        // check there wasn't an error
        $this->assertFalse($adjustment->isError(), '$adjustment has an error');
        $this->assertEquals(201, $response->getStatusCode(), 'Expected status code 201');

        // check for a couple of attributes on the $adjustment object
        $this->assertNotEmpty($adjustment['id'], '$adjustment["id"] was empty');
        $this->assertTrue($adjustment['success'], '$adjustment["success"] was not true');
        $this->assertEquals(1099, $adjustment['amount_in_cents'], '$adjustment["amount_in_cents"] mismatch');
        $this->assertEquals('Test Memo', $adjustment['memo'], '$adjustment["memo"] mismatch');
    }

    public function testNoAmountCreatesError()
    {
        $chargify = ClientHelper::getInstance('adjustment.create.error.no_amount');

        $adjustment = $chargify->adjustment()
            ->create(123);

        $response = $adjustment->getService()->getLastResponse();

        // $adjustment object should indicate an error
        $this->assertTrue($adjustment->isError(), '$adjustment was not en error');
        $this->assertEquals(422, $response->getStatusCode(), 'Expected status code 422');

        // get errors from $adjustment
        $errors = $adjustment->getErrors();

        // check for error messages
        $this->assertContains('Amount: is not a number.', $errors);
    }
}