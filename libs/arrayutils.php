<?php
/**
 * arrayutils.php
 *
 * @package jp_teleios
 * @category libs
 * @author Takahiro Kotake
 * @license Teleios Development
 */
namespace jp\teleios\libs;

class ArrayUtils
{

    /**
     * 指定した変数は連想配列か確認する
     * @param  array   $arr    連想配列か確認する変数
     * @param  boolean $strict 連想キーに数値を許容するか。(true: 許容しない、false: 許容する)
     * @return bool            連想配列の場合は true を、そうでない場合は false を返す
     */
    static function isMap(array $arr, bool $strict = true): bool
    {
        if (!is_array($arr)) {
            return false;
        }

        $stringCount = 0;
        $intCount = 0;
        foreach ($arr as $key => $value) {
            if (is_string($key)) {
                $stringCount += 1;
            } else {
                $intCount += 1;
            }
        }

        return ($strict ? ($intCount == 0 ? true : false) : ($stringCount > 0 ? true : false));
    }
}
