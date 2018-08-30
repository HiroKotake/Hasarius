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
    /**
     * 事前のインデント数
     * @var int
     */
    private $indentNumber = 0;

    public function __construct(string $closeTag = null, array $subCommand = [], int $indentNumber = 0)
    {
        $this->closeTag   = $closeTag ?? "";
        $this->subCommand = $subCommand ?? [];
        $this->indentNumber = $indentNumber ?? 0;
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

    /**
     * 自動インデントを設定
     * @param bool $autoIndent 自動インデント設定
     */
    public function setIndentNumber(int $indentNumber): void
    {
        $this->indentNumber = $indentNumber;
    }
    /**
     * 自動インデント設定を取得
     * @return bool 自動インデント設定
     */
    public function getIndentNumber(): int
    {
        return $this->indentNumber;
    }
}
