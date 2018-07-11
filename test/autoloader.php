<?php
namespace Hasarius\test;

require('../autoloader.php');
use PHPUnit\Framework\TestCase;
use Hasarius\AutoLoader;
use Hasarius\system as System;

class TestParser extends TestCase
{

    public function testAuotload()
    {
        $autoloader = new Autoloader();
        $autoloader->autoload();

        $generate = new System\Generate();
        $check = $generate->physicalTest();
        $this->assertEquals('DONE', $check);
    }
}
