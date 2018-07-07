<?php
/**
 * validater.php
 *
 * @package hasarius
 * @category utility
 * @author Takahiro Kotake
 * @license Teleios Development
 */
namespace Hasarius\Utils;

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
     * @param  int     $number     確認する数値文字列
     * @param  int     $maxLength 最大桁数（ディフォルト：0...チェックしない)
     * @return boolean             合致する場合は真を、合致しない場合は偽を返す
     */
    public static function isInteger($number, int $maxLength = 0): boolean
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
     * 指定された文字列は浮動小数値で、さらに整数部が指定最大桁数以内か判定
     * @param  float   $number             確認する浮動小数値文字列
     * @param  int     $maxIntegerLength 整数部最大桁数（ディフォルト：0...チェックしない)
     * @param  int     $maxDecimalLength 少数部最大桁数（ディフォルト：0...チェックしない)
     * @return boolean                     合致する場合は真を、合致しない場合は偽を返す
     */
    public static function isFloat(
        $number,
        int $maxIntegerLength = 0,
        int $maxDecimalLength = 0
    ): boolean {
        $checkNumber = str_replace(',', '', $number);
        $result = is_float($checkNumber);   // 整数部最大桁数指定ナシ、小数部最大桁数指定ナシを初期値とする

        list($integerNumber, $decimalNumber) = explode('.', $checkNumber);
        if ($maxIntegerLength > 0 && $maxDecimalLength == 0) {
            // 整数部最大桁数指定アリ、小数部最大桁数指定ナシ
            $result = (is_float($checkNumber) && strlen($integerNumber) <= $maxIntegerLength);
        } elseif ($maxIntegerLength == 0 && $maxDecimalLength > 0) {
            // 整数部最大桁数指定ナシ、小数部最大桁数指定アリ
            $result = (is_float($checkNumber) && strlen($decimalNumber) <= $maxDecimalLength);
        } else {
            // 整数部最大桁数指定アリ、小数部最大桁数指定アリ
            $result = (is_float($checkNumber) && strlen($integerNumber) <= $maxIntegerLength && strlen($decimalNumber) <= $maxDecimalLength);
        }
        return $result;
    }

    /**
     * 指定された文字列は文字のみで構成され、さらに指定最大文字数以内か判定
     * @param  string  $str        確認する文字列
     * @param  integer $maxLength 最大文字列長
     * @return boolean             合致する場合は真を、合致しない場合は偽を返す
     */
    public static function isString(string $str, int $maxLength = 0): boolean
    {
        $result = is_string($str);
        if ($maxLength > 0) {
            $result = (is_string($str) && (strlen($str) <= $maxLength));
        }
        return $result;
    }

    /**
     * 指定した文字列が指定した配列に含まれているか判定
     * @param  string  $str            確認する文字列
     * @param  array   $list           確認する配列
     * @param  boolean $allLeterType 大文字小文字を無視して判定するか(ディフォルト：true ... 無視して判定)
     * @return string                  配列に含まれている場合は配列に含まている適合する文字列を返す。ない場合は null を返す
     */
    public static function inList(string $str, array &$list, bool $allLeterType = true): string
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
