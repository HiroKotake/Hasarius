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
}
