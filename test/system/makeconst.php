<?php

use Hasarius\system\MakeConst;
use PHPUnit\Framework\TestCase;

class TestMakeConst extends TestCase
{

    public function testLoad()
    {
        MakeConst::load();

        $script = [
            "None" => [
                "HEAD_READY" => [
                    "Open" => "Windows.onLoad = function() {",
                    "Close" => "}"
                ]
            ],
            "JQuery" => [
                "HEAD_READY" => [
                    "Open" => "$(function)(){",
                    "Close" => "});"
                ]
            ]
        ];
        $script_file = "ScriptFile";
        $css_file = "CssFile";

        $this->assertEquals(SCRIPT, $script);
        $this->assertEquals(SCRIPT_FILE, $script_file);
        $this->assertEquals(CSS_FILE, $css_file);
    }
}
