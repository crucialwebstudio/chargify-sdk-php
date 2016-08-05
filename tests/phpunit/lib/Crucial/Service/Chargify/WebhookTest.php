<?php

/**
 * Class Crucial_Service_Chargify_WebhookTest
 *
 */
class Crucial_Service_Chargify_WebhookTest extends PHPUnit_Framework_TestCase
{
    public function testFakeWebhookHandler()
    {
        $chargify = ClientHelper::getInstance();
        $webhook = $chargify->webhook()->handleFake();

        $this->assertInternalType('string', $webhook->getEvent());
        $this->assertInternalType('int', $webhook->getId());
        $this->assertInternalType('array', $webhook->getPayload());
    }
}
