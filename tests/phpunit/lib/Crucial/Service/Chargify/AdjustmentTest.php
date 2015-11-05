<?php
use GuzzleHttp\Subscriber\Mock;


/**
 * Class Crucial_Service_Chargify_AdjustmentTest
 *
 */
class Crucial_Service_Chargify_AdjustmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @todo assert http response code
     */
    public function testCreateSuccess()
    {
        $chargify = ClientHelper::getInstance();

        // set a mock response on the client
        $mock = new Mock([
            MockResponse::read('adjustment.create.success')
        ]);
        $chargify->getHttpClient()->getEmitter()->attach($mock);

        $adjustment = $chargify->adjustment()
            ->setAmountInCents(1099)
            ->setMemo('Test Memo')
            ->setAdjustmentMethod('target')
            ->create(123);

        // check there wasn't an error
        $this->assertFalse($adjustment->isError(), '$adjustment has an error');

        // check for a couple of attributes on the $adjustment object
        $this->assertNotEmpty($adjustment['id'], '$adjustment["id"] was empty');
        $this->assertTrue($adjustment['success'], '$adjustment["success"] was not true');
        $this->assertEquals(1099, $adjustment['amount_in_cents'], '$adjustment["amount_in_cents"] mismatch');
        $this->assertEquals('Test Memo', $adjustment['memo'], '$adjustment["memo"] mismatch');
    }


    /**
     * @todo assert http response code
     */
    public function testNoAmountCreatesError()
    {
        $chargify = ClientHelper::getInstance();

        // set a mock response on the client
        $mock = new Mock([
            MockResponse::read('adjustment.create.error.no_amount')
        ]);
        $chargify->getHttpClient()->getEmitter()->attach($mock);

        $adjustment = $chargify->adjustment()
            ->create(123);

        // $adjustment object should indicate an error
        $this->assertTrue($adjustment->isError(), '$adjustment was not en error');

        // get errors from $adjustment
        $errors = $adjustment->getErrors();

        // check for error messages
        $this->assertContains('Amount: is not a number.', $errors);
    }
}