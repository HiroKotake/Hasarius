<?php

namespace Hasarius\test\utils;

use Hasarius\utils\StrUtils;
use PHPUnit\Framework\TestCase;

class TestStrUtils extends TestCase
{
    public function provideIndentRepeat()
    {
        $verifiyInfo = [];
        // インテント数 2、インデントの半角空白 4、半角空白
        $verifiyInfo[] = [2, 4, null, "        "];
        // インテント数 3、インデントの半角空白 0、タブ
        $verifiyInfo[] = [3, 0, "\t", "\t\t\t"];
        // インテント数 2、インデントの文字数 6、'.'
        $verifiyInfo[] = [2, 6, ".", "............"];

        return $verifiyInfo;
    }

    /** @dataProvider provideIndentRepeat */
    public function testIndentRepeat($count, $number, $whitespace, $expect)
    {
        if (empty($whitespace)) {
            $result = StrUtils::indentRepeat($count, $number);
        } else {
            $result = StrUtils::indentRepeat($count, $number, $whitespace);
        }
        $this->assertEquals($result, $expect);
    }
}
