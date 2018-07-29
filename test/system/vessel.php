<?php
namespace Hasarius\test\system;

use Hasarius\system\Vessel;
use PHPUnit\Framework\TestCase;

class TestVessel extends TestCase
{
    public function provideSetGet()
    {
        return [
            ['command','test'],
            ['paramaters',['param1'=>'value1', 'param2'=>'value2', 'param3'=>'value3']],
            ['modifiers', ['param1'=>'value1', 'param2'=>'value2', 'param3'=>'value3']],
            ['text', 'test_string'],
            ['comment', 'comment_string'],
            ['lineNumber', 100],
            ['id', 'id_100'],
            ['tagOpen', 'test'],
            ['tagClose', '</test>'],
            ['css', 'css string'],
            ['blockType', 1],
        ];
    }

    /** @dataProvider provideSetGet */
    public function testSetGet($key, $value)
    {
        $vessel = new Vessel();
        $setCommand = 'set' . ucfirst($key);
        $getCommand = 'get' . ucfirst($key);
        $vessel->$setCommand($value);
        $result = $vessel->$getCommand();

        $this->assertEquals($value, $result);
    }

    public function provideAdd()
    {
        return [
            ['paramaters', ['param1'=>'value1', 'param2'=>'value2'], ['key' => 'param3', 'val' => 'value3'], ['param1'=>'value1', 'param2'=>'value2', 'param3'=>'value3']],
            ['modifiers', ['param1'=>'value1', 'param2'=>'value2'], ['key' => 'param3', 'val' => 'value3'], ['param1'=>'value1', 'param2'=>'value2', 'param3'=>'value3']]
        ];
    }

    /** @dataProvider provideAdd */
    public function testAdd($command, $initData, $addData, $expect)
    {
        $vessel = new Vessel();
        $setCommand = 'set' . ucfirst($command);
        $addCommand = 'add' . ucfirst($command);
        $getCommand = 'get' . ucfirst($command);

        $vessel->$setCommand($initData);
        $vessel->$addCommand($addData['key'], $addData['val']);
        $result = $vessel->$getCommand();

        $this->assertEquals($expect, $result);
    }

    public function provideScript()
    {
        return [
            ['file', 'file script'],
            ['head', 'header script'],
            ['body', 'body script']
        ];
    }

    /** @dataProvider provideScript */
    public function testScript($key, $val)
    {
        $vessel = new Vessel();
        $vessel->setScript($key, $val);
        $result = $vessel->getScript($key);

        $this->assertEquals($val, $result);
    }
}
