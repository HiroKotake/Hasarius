<?php

require_once("../../utils/parser.php");
use Hasarius\utils\Parser;
use PHPUnit\Framework\TestCase;

class TestParser extends TestCase
{

    public function testAnalyzeLine()
    {
        // コマンド、属性、本文
        $str = '#div id="test" class="block_left" name="div test" div is block tag.';
        $result = Parser::analyzeLine($str);
        $compareData = [
            'command' => 'div',
            'paramaters' => [
                'id' => 'test',
                'class' => 'block_left',
                'name' => 'div test'
            ],
            'modifiers' => [],
            'text' => 'div is block tag.',
            'comment' => '',
        ];
        $this->assertEquals($compareData, $result, 0, 0, true);   // 配列の順序を意識しないで比較

        // 属性エスケープ確認：コマンド、属性、本文
        $str = '#div id="test" class="block_left" name\="div test" div is block tag.';
        $result = Parser::analyzeLine($str);
        $compareData = [
            'command' => 'div',
            'paramaters' => [
                'id' => 'test',
                'class' => 'block_left',
            ],
            'modifiers' => [],
            'text' => 'name="div test" div is block tag.',
            'comment' => '',
        ];
        $this->assertEquals($compareData, $result, 0, 0, true);   // 配列の順序を意識しないで比較

        // 本文のみ
        $str = 'div is block tag.';
        $result = Parser::analyzeLine($str);
        $compareData = [
            'command' => "",
            'paramaters' => [],
            'modifiers' => [],
            'text' => 'div is block tag.',
            'comment' => '',
        ];
        $this->assertEquals($compareData, $result, 0, 0, true);   // 配列の順序を意識しないで比較

        // 本文のみ：インラインコマンド付き
        $str = 'div is @b block tag@.';
        $result = Parser::analyzeLine($str);
        $compareData = [
            'command' => "",
            'paramaters' => [],
            'modifiers' => [
                '@b block tag@'
            ],
            'text' => 'div is @b block tag@.',
            'comment' => '',
        ];
        $this->assertEquals($compareData, $result, 0, 0, true);   // 配列の順序を意識しないで比較

        // コマンド、属性、本文(インラインコマンド付き)
        $str = '#div id="test" class="block_left" name="div test" div is @b block tag@.';
        $result = Parser::analyzeLine($str);
        $compareData = [
            'command' => 'div',
            'paramaters' => [
                'id' => 'test',
                'class' => 'block_left',
                'name' => 'div test'
            ],
            'modifiers' => [
                '@b block tag@'
            ],
            'text' => 'div is @b block tag@.',
            'comment' => '',
        ];
        $this->assertEquals($compareData, $result, 0, 0, true);   // 配列の順序を意識しないで比較

        // コマンド、属性、本文(インラインコマンド付き)、コメント
        $str = '#div id="test" class="block_left" name="div test" div is @b block tag@. // Test Case Comment';
        $result = Parser::analyzeLine($str);
        $compareData = [
            'command' => 'div',
            'paramaters' => [
                'id' => 'test',
                'class' => 'block_left',
                'name' => 'div test'
            ],
            'modifiers' => [
                '@b block tag@'
            ],
            'text' => 'div is @b block tag@.',
            'comment' => 'Test Case Comment',
        ];
        $this->assertEquals($compareData, $result, 0, 0, true);   // 配列の順序を意識しないで比較

        // コマンド、属性、本文(インラインコマンド付き、コメントエスケープを含む)、コメント
        $str = '#div id="test" class="block_left" name="div test" \/\/ div is @b block tag@. // Test Case Comment';
        $result = Parser::analyzeLine($str);
        $compareData = [
            'command' => 'div',
            'paramaters' => [
                'id' => 'test',
                'class' => 'block_left',
                'name' => 'div test'
            ],
            'modifiers' => [
                '@b block tag@'
            ],
            'text' => '// div is @b block tag@.',
            'comment' => 'Test Case Comment',
        ];
        $this->assertEquals($compareData, $result, 0, 0, true);   // 配列の順序を意識しないで比較

        // コマンド開始文字変更('#'->'&')：コマンド、属性、本文(インラインコマンド付き、コメントエスケープを含む)、コメント
        $str = '&div id="test" class="block_left" name="div test" \/\/ div is @b block tag@. // Test Case Comment';
        $result = Parser::analyzeLine($str, '&');
        $compareData = [
            'command' => 'div',
            'paramaters' => [
                'id' => 'test',
                'class' => 'block_left',
                'name' => 'div test'
            ],
            'modifiers' => [
                '@b block tag@'
            ],
            'text' => '// div is @b block tag@.',
            'comment' => 'Test Case Comment',
        ];
        $this->assertEquals($compareData, $result, 0, 0, true);   // 配列の順序を意識しないで比較

        // コマンド開始文字変更('#'->'&')、属性区切り文字変更('='->':')：コマンド、属性、本文(インラインコマンド付き、コメントエスケープを含む)、コメント
        $str = '&div id:"test" class:"block_left" name:"div test" \/\/ div is @b block tag@. // Test Case Comment';
        $result = Parser::analyzeLine($str, '&', ':');
        $compareData = [
            'command' => 'div',
            'paramaters' => [
                'id' => 'test',
                'class' => 'block_left',
                'name' => 'div test'
            ],
            'modifiers' => [
                '@b block tag@'
            ],
            'text' => '// div is @b block tag@.',
            'comment' => 'Test Case Comment',
        ];
        $this->assertEquals($compareData, $result, 0, 0, true);   // 配列の順序を意識しないで比較
    }
}
