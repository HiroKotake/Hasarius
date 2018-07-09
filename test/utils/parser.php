<?php

require_once("../../utils/parser.php");
use Hasarius\utils\Parser;
use PHPUnit\Framework\TestCase;

class TestParser extends TestCase
{

    public function testAnalyzeLine()
    {
        $str = '#div id="test" class="block_left"';
        $result = Parser::analyzeLine($str, '#', '=');
        echo PHP_EOL;
        echo '[DEBUG]' . PHP_EOL;
        var_dump($result);
    }
}
