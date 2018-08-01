<?php
/**
 * closerinfo.php
 *
 * @package Hasarius
 * @category system
 * @author Takahiro Kotake
 * @license Teleios Development
 */

namespace Hasarius\system;

class CloserInfo
{
    /**
     * クローズタグ
     * @var string クローズタグ文字列
     */
    private $closeTag = "";
    /**
     * サブコマンドデータリスト
     * @var array サブコマンドデータリスト
     */
    private $subCommand = [];

    public function __construct(string $closeTag, array $subCommand)
    {
        $this->closeTag   = $closeTag;
        $this->subCommand = $subCommand;
    }

    /**
     * クローズタグを設定
     * @param string $closeTag クローズタグ文字列
     */
    public function setCloseTag(string $closeTag): void
    {
        $this->closeTag = $closeTag;
    }
    /**
     * クローズタグを取得
     * @return string クローズタグ文字列
     */
    public function getCloseTag(): string
    {
        return $this->closeTag;
    }

    /**
     * サブコマンドデータリストを設定
     * @param array $subCommand サブコマンドデータリスト
     */
    public function setSubCommand(array $subCommand): void
    {
        $this->subCommand = $subCommand;
    }
    /**
     * サブコマンドデータリストを取得
     * @return array サブコマンドデータリスト サブコマンドデータリストが存在しない場合は空配列を返す
     */
    public function getSubCommand(): array
    {
        return $this->subCommand;
    }
}
