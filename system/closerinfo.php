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
    /**
     * 自動改行設定
     * @var bool
     */
    private $autoLineBreak = true;

    public function __construct(string $closeTag = null, array $subCommand = [], int $indentNumber = 0, bool $autoLineBreak = true)
    {
        $this->closeTag   = $closeTag ?? "";
        $this->subCommand = $subCommand ?? [];
        $this->indentNumber = $indentNumber ?? 0;
        $this->autoLineBreak = $autoLineBreak;
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

    /**
     * 自動改行設定を行う
     * @param bool $lineBreak 自動改行フラグ
     */
    public function setAutoLineBreak(bool $lineBreak): void
    {
        $this->autoLineBreak = $lineBreak;
    }

    /**
     * 自動改行設定か確認
     * @return bool true であれば自動改行、false であれば手動改行
     */
    public function isAutoLineBreak(): bool
    {
        return $this->autoLineBreak;
    }
}
