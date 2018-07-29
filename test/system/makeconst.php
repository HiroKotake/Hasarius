<?php
namespace Hasarius\test\system;

use Hasarius\system\MakeConst;
use PHPUnit\Framework\TestCase;

class TestMakeConst extends TestCase
{

    public function testLoad()
    {
        MakeConst::load();

        $script = [
            "None" => [
                "READY" => [
                    "Open" => "Windows.onLoad = function() {",
                    "Close" => "}"
                ]
            ],
            "JQuery" => [
                "READY" => [
                    "Open" => "$(function)(){",
                    "Close" => "});"
                ]
            ]
        ];
        $scriptFile = "ScriptFile";
        $cssFile = "CssFile";
        $tagAttributes = [
            "id" => [
                "alias" => "id",
                "type" => "string"
            ]
        ];
        $cssAttributes = [
            "width" => [
                "alias" => "w",
                "type" => "numeric",
                "subtype" => ["numeric", "px", "percent"]
            ]
        ];

        $this->assertEquals(SCRIPT, $script);
        $this->assertEquals(SCRIPT_FILE, $scriptFile);
        $this->assertEquals(CSS_FILE, $cssFile);
        $this->assertEquals(TAG_ATTRIBUTES, $tagAttributes);
        $this->assertEquals(CSS_ATTRIBUTES, $cssAttributes);
    }
}
