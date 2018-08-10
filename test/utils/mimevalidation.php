<?php
namespace Hasarius\test\utils;

use Hasarius\utils\MimeValidation;
use PHPUnit\Framework\TestCase;

class TestMimeValidation extends TestCase
{

    public function provideValidatieMime()
    {
        $params = [];

        $params[] = ["application/hta", true];
        $params[] = ["application/hoge", false];
        $params[] = ["audio/wav", true];
        $params[] = ["image/png", true];
        $params[] = ["text/rtf", true];

        return $params;
    }

    /** @dataProvider provideValidatieMime */
    public function testValidatieMime(string $mime, bool $check)
    {
        $result = MimeValidation::validatieMime($mime);
        $this->assertEquals($result, $check);
    }
}
