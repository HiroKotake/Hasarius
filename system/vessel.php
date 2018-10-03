<?php
/**
 * vessel.php
 *
 * @package Hasarius
 * @category system
 * @author Takahiro Kotake
 * @license Teleios Development
 */

namespace Hasarius\system;

class Vessel
{
    private $attributes = [
        // For Parser
        'command'            => "",
        'paramaters'         => [],
        'modifiers'          => [],
        'text'               => "",
        'comment'            => "",
        'subCommand'         => false,
        // For Genarater
        'lineNumber'         => 0,
        'indent'             => 0,  // インデントの回数を設定
        // For Command
        'id'                 => "",
        'blockType'          => 0,
        'tagOpen'            => "",
        'tagClose'           => "",
        'verifiedAttribute'  => "", // 属性をまとめたテキスト
        'script'             => [],
        'css'                => "",
        'autoIndent'         => true,
        'autoLineBreak'      => false,
        // For Command(Batch)
        'batch'              => [],
    ];

    // Setter, Getter & Adder
    public function __call($method, $arguments)
    {
        $matches = null;
        preg_match('/^(set|get|add)(.*)$/u', $method, $matches);
        $command = $matches[1];
        $valName = lcfirst($matches[2]);
        switch ($command) {
            case 'set':
                if ($valName == "script") {
                    if (!array_key_exists($arguments[0], $this->attributes["script"])) {
                        $this->attributes["script"][$arguments[0]] = "";
                    }
                    $this->attributes["script"][$arguments[0]] = $arguments[1];
                } else {
                    $this->attributes[$valName] = $arguments[0];
                }
                break;
            case 'get':
                if ($valName == "script" && !empty($arguments) && array_key_exists($arguments[0], $this->attributes["script"])) {
                    return $this->attributes["script"][$arguments[0]];
                }
                return $this->attributes[$valName];
                break;
            case 'add':
                if (!array_key_exists($valName, $this->attributes)) {
                    $this->attributes[$valName] = [];
                }
                if (!is_array($this->attributes[$valName])) {
                    break;
                }
                $this->attributes[$valName][$arguments[0]] = $arguments[1];
                break;
        }
    }

    /**
     * idが設定されているか確認する。
     * @return bool 設定されている場合はtrueを、されていない場合はfalseを返す
     */
    public function idExistsParamaters(): bool
    {
        return (array_key_exists("id", $this->attributes["paramaters"]) && !empty($this->attributes["paramaters"]["id"]));
    }
    /**
     * サブコマンドか確認
     * @return bool サブコマンドの場合は真を、サブコマンドでない場合は偽を返す
     */
    public function isSubCommand(): bool
    {
        return $this->attributes['subCommand'];
    }

    /**
     * 自動インデントか確認
     * @return bool 自動インデントの場合は真を、手動インデントの場合は偽を返す
     */
    public function isAutoIndent(): bool
    {
        return $this->attributes["autoIndent"];
    }

    /**
     * 自動改行か確認
     * @return bool 自動改行の場合は真を、手動改行の場合は偽を返す
     */
    public function isAutoLineBreak(): bool
    {
        return $this->attributes["autoLineBreak"];
    }
}
