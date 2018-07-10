<?php
/**
 * div.php
 * @var [type]
 */
namespace Hasarius\commands;

use Hasarius\system as System;

/**
 * divタグクラス
 */
class CommandDiv extends System\Command
{

    public function __construct()
    {
        $jsonFile = __DIR__ . DIRECTORY_SEPARATOR . 'div.json';  // コマンド設定用JSONファイルを指定
        parent::__construct($jsonFile);
    }
}
