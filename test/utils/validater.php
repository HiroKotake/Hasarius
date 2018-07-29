<?php
namespace Hasarius\test\utils;

use Hasarius\utils\Validater;
use PHPUnit\Framework\TestCase;

class TestValidater extends TestCase
{
    public function testIsInteger(): void
    {
        $testResult = Validater::isInteger(123);
        $this->assertEquals(true, $testResult);

        $testResult = Validater::isInteger("123,456");
        $this->assertEquals(true, $testResult);

        $testResult = Validater::isInteger("123,456", 5);
        $this->assertEquals(false, $testResult);

        $testResult = Validater::isInteger("string");
        $this->assertEquals(false, $testResult);
    }

    public function testIsFloat(): void
    {
        $testResult = Validater::isFloat(123.45);
        $this->assertEquals(true, $testResult);

        $testResult = Validater::isFloat(123.45, 3, 2);
        $this->assertEquals(true, $testResult);

        $testResult = Validater::isFloat('12.3.45');
        $this->assertEquals(false, $testResult);

        $testResult = Validater::isFloat('12C.45');
        $this->assertEquals(false, $testResult);

        $testResult = Validater::isFloat(123.45, 1, 1);
        $this->assertEquals(false, $testResult);

        $testResult = Validater::isFloat(3.4, 1, 1);
        $this->assertEquals(true, $testResult);

        $testResult = Validater::isFloat(3.4, 1);
        $this->assertEquals(true, $testResult);

        $testResult = Validater::isFloat(23.4, 1);
        $this->assertEquals(false, $testResult);

        $testResult = Validater::isFloat(123.4, 0, 1);
        $this->assertEquals(true, $testResult);
    }

    public function testIsSting(): void
    {
        $testResult = Validater::isString("Test String.");
        $this->assertEquals(true, $testResult);

        $testResult = Validater::isString("Test String.", 20);
        $this->assertEquals(true, $testResult);

        $testResult = Validater::isString("Test String.", 5);
        $this->assertEquals(false, $testResult);

        $testResult = Validater::isString(23456);
        $this->assertEquals(true, $testResult);
    }

    public function testInList(): void
    {
        $testArray = [
            'Apple',
            'Berry',
            'Cherry',
            'Durian',
            'Grape'
        ];

        $testResult = Validater::inList('Apple', $testArray);
        $this->assertEquals('Apple', $testResult);

        $testResult = Validater::inList('APPLE', $testArray);
        $this->assertEquals('Apple', $testResult);

        $testResult = Validater::inList('APPLE', $testArray, false);
        $this->assertNull($testResult);

        $testResult = Validater::inList('pineapple', $testArray);
        $this->assertNull($testResult);
    }
}
