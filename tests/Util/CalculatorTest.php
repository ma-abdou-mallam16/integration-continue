<?php

namespace App\Test\Util;

use App\Util\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function testAdd()
    {
        $calculator = new Calculator();
        $result = $calculator->add(23, 13);

        $this->assertEquals(36, $result);
    }
}
