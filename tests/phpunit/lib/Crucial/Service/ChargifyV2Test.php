<?php

class Crucial_Service_ChargifyV2Test extends PHPUnit_Framework_TestCase
{
    public function testHelperInstances()
    {
        $chargify = ClientV2Helper::getInstance();

        $direct = $chargify->direct();
        $this->assertInstanceOf('Crucial\Service\ChargifyV2\Direct', $direct);

        $call = $chargify->call();
        $this->assertInstanceOf('Crucial\Service\ChargifyV2\Call', $call);
    }
}