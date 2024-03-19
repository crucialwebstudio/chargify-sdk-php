<?php

namespace Test\Crucial\Service;

use PHPUnit\Framework\TestCase;
use Test\Helpers\ClientV2Helper;

class ChargifyV2Test extends TestCase
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