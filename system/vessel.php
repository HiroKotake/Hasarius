<?php

namespace Hasarius\system;

class Vessel
{

    // Original Text
    private $source = NULL;
    // ID
    private $id = NULL;
    // 行番号
    private $line_number = 0;
    // TreeID
    private $tree_id = "";
    // 親TreeID
    private $parent_tree_id = "";
    // コマンド名
    private $command_name = "";
    // 親コマンド名
    private $parent_command_name = "";
    // パラメータ
    private $parameters = [];
    // 装飾コマンド
    private $decorations = [];
    // テキスト
    private $text = NULL;

    function __construct(string $line)
    {
        $this->source = $line;
    }


}