<?php

use Hasarius\utils\Parser;
use Hasarius\system\Vessel;
use PHPUnit\Framework\TestCase;

class TestParser extends TestCase
{

    public function provideAnalyzeLine()
    {
        $command = [];
        // コマンド、属性、本文
        $command[] = [
            'source' => '#div id="test" class="block_left" name="div test" div is block tag.',
            'expects' => [
                'command' => 'div',
                'paramaters' => [
                    'id' => 'test',
                    'class' => 'block_left',
                    'name' => 'div test'
                ],
                'modifiers' => [],
                'text' => 'div is block tag.',
                'comment' => '',
            ],
            'commandHead' => null,
            'attributeDelime' => null,
        ];

        // 属性エスケープ確認：コマンド、属性、本文
        $command[] = [
            'source' => '#div id="test" class="block_left" name\="div test" div is block tag.',
            'expects' => [
                'command' => 'div',
                'paramaters' => [
                    'id' => 'test',
                    'class' => 'block_left',
                ],
                'modifiers' => [],
                'text' => 'name="div test" div is block tag.',
                'comment' => '',
            ],
            'commandHead' => null,
            'attributeDelime' => null,
        ];

        // 本文のみ
        $command[] = [
            'source' => 'div is block tag.',
            'expects' => [
                'command' => "",
                'paramaters' => [],
                'modifiers' => [],
                'text' => 'div is block tag.',
                'comment' => '',
            ],
            'commandHead' => null,
            'attributeDelime' => null,
        ];

        // 本文のみ：インラインコマンド付き
        $command[] = [
            'source' => 'div is @b block tag@.',
            'expects' => [
                'command' => "",
                'paramaters' => [],
                'modifiers' => [
                    '@b block tag@'
                ],
                'text' => 'div is @b block tag@.',
                'comment' => '',
            ],
            'commandHead' => null,
            'attributeDelime' => null,
        ];

        // コマンド、属性、本文(インラインコマンド付き)
        $command[] = [
            'source' => '#div id="test" class="block_left" name="div test" div is @b block tag@.',
            'expects' => [
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
            ],
            'commandHead' => null,
            'attributeDelime' => null,
        ];

        // コマンド、属性、本文(インラインコマンド付き)、コメント
        $command[] = [
            'source' => '#div id="test" class="block_left" name="div test" div is @b block tag@. // Test Case Comment',
            'expects' => [
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
            ],
            'commandHead' => null,
            'attributeDelime' => null,
        ];

        // コマンド、属性、本文(インラインコマンド付き、コメントエスケープを含む)、コメント
        $command[] = [
            'source' => '#div id="test" class="block_left" name="div test" \/\/ div is @b block tag@. // Test Case Comment',
            'expects' => [
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
            ],
            'commandHead' => null,
            'attributeDelime' => null,
        ];

        // コマンド開始文字変更('#'->'&')：コマンド、属性、本文(インラインコマンド付き、コメントエスケープを含む)、コメント
        $command[] = [
            'source' => '&div id="test" class="block_left" name="div test" \/\/ div is @b block tag@. // Test Case Comment',
            'expects' => [
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
            ],
            'commandHead' => '&',
            'attributeDelime' => null,
        ];

        // コマンド開始文字変更('#'->'&')、属性区切り文字変更('='->':')：コマンド、属性、本文(インラインコマンド付き、コメントエスケープを含む)、コメント
        $command[] = [
            'source' => '&div id:"test" class:"block_left" name:"div test" \/\/ div is @b block tag@. // Test Case Comment',
            'expects' => [
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
            ],
            'commandHead' => '&',
            'attributeDelime' => ':',
        ];
        return $command;
    }
    /** @dataProvider provideAnalyzeLine */
    public function testAnalyzeLine($source, $expects, $commandHead, $attributeDelime)
    {
        if (empty($commandHead)) {
            $vessel = Parser::analyzeLine($source);
        } else {
            if (empty($attributeDelime)) {
                $vessel = Parser::analyzeLine($source, $commandHead);
            } else {
                $vessel = Parser::analyzeLine($source, $commandHead, $attributeDelime);
            }
        }

        if (array_key_exists('command', $expects)) {
            $this->assertEquals($vessel->getCommand(), $expects['command']);
        }
        if (array_key_exists('paramaters', $expects)) {
            $this->assertEquals($vessel->getParamaters(), $expects['paramaters']);
        }
        if (array_key_exists('modifiers', $expects)) {
            $this->assertEquals($vessel->getModifiers(), $expects['modifiers']);
        }
        if (array_key_exists('text', $expects)) {
            $this->assertEquals($vessel->getText(), $expects['text']);
        }
        if (array_key_exists('comment', $expects)) {
            $this->assertEquals($vessel->getComment(), $expects['comment']);
        }
    }
}
