<?php
/**
 * div.php
 *
 * @package Hasarius
 * @category commands
 * @author Takahiro Kotake
 * @license Teleios Development
 */
namespace Hasarius\commands;

use Hasarius\system\Command;

/**
 * divタグクラス
 */
class CommandDiv extends Command
{

    public function __construct()
    {
        $jsonFile = __DIR__ . DIRECTORY_SEPARATOR . 'div.json';  // コマンド設定用JSONファイルを指定
        parent::__construct($jsonFile);
    }
}
