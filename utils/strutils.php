<?php
/**
 * strutils.php
 *
 * @package Hasarius
 * @category utils
 * @author Takahiro Kotake
 * @license Teleios Development
 */
namespace Hasarius\utils;

class StrUtils
{
    /**
     * インデントスペースを生成する
     * @param  int     $count      インデント数
     * @param  integer $number     半角スペースでの基準数
     * @param  string  $whitespace 使用するホワイトスペース文字
     * @return string              生成後のインデント
     */
    public static function indentRepeat(int $count, int $number = 4, string $whitespace = ' '): string
    {
        $number = $number != 0 ? $number : 4;   // 0に対する保険

        $indent = "";
        for ($i = 0; $i < $count; $i++) {
            if (preg_match('/(\t|\r|\n|\f|\v|\r\n)/', $whitespace) > 0) {
                $indent .= $whitespace;
            } else {
                $indent .= str_repeat($whitespace, $number);
            }
        }
        return $indent;
    }
}
