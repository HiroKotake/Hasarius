<?php
/**
 * validater.php
 *
 * @package jp_teleios
 * @category libs
 * @author Takahiro Kotake
 * @license Teleios Development
 */
namespace jp\teleios\libs;

/**
 * Validater Class
 *
 * @package hasarius
 * @category utility
 * @author Takahiro Kotake
 */
class Validater
{
    /**
     * 指定された文字列は数値で、さらに指定最大桁数以内か判定
     * @param  int     $number    確認する数値文字列
     * @param  int     $maxLength 最大桁数（ディフォルト：0...チェックしない)
     * @return bool               合致する場合は真を、合致しない場合は偽を返す
     */
    public static function isInteger($number, int $maxLength = 0): bool
    {
        $checkNumber = str_replace(',', '', $number);
        $result = is_numeric($checkNumber); // 最大桁数指定ナシを初期値とする
        if ($maxLength > 0) {
            // 最大桁数指定アリ
            $result = (is_numeric($checkNumber) && strlen($checkNumber) <= $maxLength);
        }
        return $result;
    }

    /**
     * 指定された文字列は浮動小数値で、さらに整数部,指数部が各々指定最大桁数以内か判定
     * @param  float   $number           確認する浮動小数値文字列
     * @param  int     $maxIntegerLength 整数部最大桁数（ディフォルト：0...チェックしない)
     * @param  int     $maxDecimalLength 少数部最大桁数（ディフォルト：0...チェックしない)
     * @return bool                      合致する場合は真を、合致しない場合は偽を返す
     */
    public static function isFloat(
        $number,
        int $maxIntegerLength = 0,
        int $maxDecimalLength = 0
    ): bool {
        // 小数点の数をチェック:小数点が複数ある場合はアウト
        $checkArray = explode('.', $number);
        if (count($checkArray) > 2) {
            return false;
        }
        // 整数部、少数部に分ける
        $integerNumber = $checkArray[0];
        $decimalNumber = $checkArray[1];
        // 整数部に数値以外が含まれているか：含まれている場合はアウト
        if (!is_numeric($integerNumber)) {
            return false;
        }
        // 小数部に数値以外が含まれているか：含まれている場合はアウト
        if (!is_numeric($decimalNumber)) {
            return false;
        }
        // 浮動小数値に再構成
        $checkNumber = (float)($integerNumber . '.' . $decimalNumber);
        $result = is_float($checkNumber);   // 整数部最大桁数指定ナシ、小数部最大桁数指定ナシを初期値とする

        // 最大桁数チェック
        if ($maxIntegerLength > 0 && $maxDecimalLength == 0) {
            // 整数部最大桁数指定アリ、小数部最大桁数指定ナシ
            $result = (strlen($integerNumber) <= $maxIntegerLength);
        } elseif ($maxIntegerLength == 0 && $maxDecimalLength > 0) {
            // 整数部最大桁数指定ナシ、小数部最大桁数指定アリ
            $result = (strlen($decimalNumber) <= $maxDecimalLength);
        } else if ($maxIntegerLength > 0 && $maxDecimalLength > 0) {
            // 整数部最大桁数指定アリ、小数部最大桁数指定アリ
            $result = (strlen($integerNumber) <= $maxIntegerLength && strlen($decimalNumber) <= $maxDecimalLength);
        }
        return $result;
    }

    /**
     * 指定された文字列は文字のみで構成され、さらに指定最大文字数以内か判定
     * @param  string  $str       確認する文字列
     * @param  integer $maxLength 最大文字列長
     * @return bool               合致する場合は真を、合致しない場合は偽を返す
     */
    public static function isString(string $str, int $maxLength = 0): bool
    {
        $result = is_string($str);
        if ($maxLength > 0) {
            $result = (is_string($str) && (mb_strlen($str) <= $maxLength));
        }
        return $result;
    }

    /**
     * 指定した文字列が指定した配列に含まれているか判定
     * @param  string  $str            確認する文字列
     * @param  array   $list           確認する配列
     * @param  bool    $allLeterType   大文字小文字を無視して判定するか(ディフォルト：true ... 無視して判定)
     * @return string                  配列に含まれている場合は配列に含まている適合する文字列を返す。ない場合は null を返す
     */
    public static function inList(string $str, array &$list, bool $allLeterType = true)
    {
        $result = in_array($str, $list) ? $str : null;
        if ($allLeterType) {
            $str = strtolower($str);
            foreach ($list as $member) {
                if (strtolower($member) == $str) {
                    $result = $member;
                    break;
                }
            }
        }
        return $result;
    }
}
