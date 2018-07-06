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
     * @param  int     $max_length 最大桁数（ディフォルト：0...チェックしない)
     * @return boolean             合致する場合は真を、合致しない場合は偽を返す
     */
    public static function integer($number, int $max_length = 0): boolean
    {
        $check_number = str_replace(',', '', $number);
        if ($max_length > 0) {
            // 最大桁数指定アリ
            return (is_numeric($check_number) && strlen($check_number) <= $max_length);
        } else {
            // 最大桁数指定ナシ
            return is_numeric($check_number);
        }
    }

    /**
     * 指定された文字列は浮動小数値で、さらに整数部が指定最大桁数以内か判定
     * @param  float   $number             確認する浮動小数値文字列
     * @param  int     $max_integer_length 整数部最大桁数（ディフォルト：0...チェックしない)
     * @param  int     $max_decimal_length 少数部最大桁数（ディフォルト：0...チェックしない)
     * @return boolean                     合致する場合は真を、合致しない場合は偽を返す
     */
    public static function float($number, int $max_integer_length = 0, int $max_decimal_length = 0): boolean
    {
        $check_number = str_replace(',', '', $number);
        list($integer_number, $decimal_number) = explode('.', $check_number);
        if ($max_integer_length > 0 && $max_decimal_length > 0) {
            // 整数部最大桁数指定アリ、小数部最大桁数指定アリ
            return (is_float($check_number) && strlen($integer_number) <= $max_integer_length && strlen($decimal_number) <= $max_decimal_length);
        } elseif ($max_integer_length > 0 && $max_decimal_length == 0) {
            // 整数部最大桁数指定アリ、小数部最大桁数指定ナシ
            return (is_float($check_number) && strlen($integer_number) <= $max_integer_length);
        } elseif ($max_integer_length == 0 && $max_decimal_length > 0) {
            // 整数部最大桁数指定ナシ、小数部最大桁数指定アリ
            return (is_float($check_number) && strlen($decimal_number) <= $max_decimal_length);
        } else {
            // 整数部最大桁数指定ナシ、小数部最大桁数指定ナシ
            return is_float($check_number);
        }
    }

    /**
     * 指定された文字列は文字のみで構成され、さらに指定最大文字数以内か判定
     * @param  string  $str        確認する文字列
     * @param  integer $max_length 最大文字列長
     * @return boolean             合致する場合は真を、合致しない場合は偽を返す
     */
    public static function string(string $str, int $max_length = 0): boolean
    {
        if ($max_length > 0) {
            return (is_string($str) && (strlen($str) <= $max_length));
        } else {
            return is_string($str);
        }
    }

    /**
     * 指定した文字列が指定した配列に含まれているか判定
     * @param  string  $str            確認する文字列
     * @param  array   $list           確認する配列
     * @param  boolean $all_leter_type 大文字小文字を無視して判定するか(ディフォルト：true ... 無視して判定)
     * @return string                  配列に含まれている場合は配列に含まている適合する文字列を返す。ない場合は null を返す
     */
    public static function in_list(string $str, array &$list, boolean $all_leter_type = true)
    {
        $result = null;
        if ($all_leter_type) {
            $str = strtolower($str);
            foreach ($list as $member) {
                if (strtolower($member) == $str) {
                    $result = $member;
                    break;
                }
            }
        } else {
            $result = in_array($str, $list) ? $str : null;
        }
        return $result;
    }

}
