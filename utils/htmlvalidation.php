<?php
/**
 * validation.php
 *
 * @package Hasarius
 * @category utils
 * @author Takahiro Kotake
 * @license Teleios Development
 */

namespace Hasarius\utils;

use Hasarius\system\Vessel;

/**
 * HTML 属性用ヴァリエーションクラス
 *
 * @package hasarius
 * @category utility
 * @author tkotake
 */
class HtmlValidation
{

    /**
     * エンコードリスト
     * ToDo: とりあえず日本語しか入っていないので、順次追加する必要がある。
     * @var array
     */
    private static $encodeList = [
        'ISO-2022-JP',
        'UTF-8',
        'Shift_JIS',
        'EUC-JP'
    ];

    /**
     * チェック用パターン一覧
     * @var array
     */
    private static $validPattern = [
        "BUTTON_TYPE" => "/^(submit|reset|button)$/u",
        "BLEAR_TYPE" => "/^(left|right|all|none)$/u",
        "DIR_TYPE" => "/^(ltr|rtl)$/",
        "FILENAME" => "/^\S*$/",
        "FONT" => "/^.*$/",     // フォントリストを持たないので空白を含まない文字列であればとりあえずOKにしておく
        "GET_POST" => "/^(get|post)$/u",
        "LANG" => "/^[a-z]{2}(-[a-z]{2})?$/",
        "LINE_FRAME" => "/^(void|lhs|rhs|vsides|above|below|hsides|box|border)$/",
        "LIST_NUM" => "/^(1|A|a|I|i)$/",
        "LINE_RULES" => "/^(none|rows|cols|groups|all)$/",
        "LINE_TYPE" => "/^(alternate|stylesheet|start|next|prev|contents|index|glossary|copyright|chapter|section|subsection|appendix|help|bookmark)$/",
        "LIST_STYLE" => "/^(disc|circle|square|1|A|a|I|i)$/",
        "LIST_SYMBOL" => "/^(disc|circle|square)$/",
        "NZ_PCT" => "/^(1|2|3|4|5|6|7|8|9)\d*\%?$/",
        "MEDIA_QUERY" => "/^(not|only)?\s*((all|screen|print|speech|tv|projection|handheld|tty|braille|embossed)?\s*((and)?\s*(\((((min-|max-)?((device-)?width:\s*\d*|(device-)?height:\s*\d*|(device-)?acept-ratio:\s*(\d*\/\d*)|color(-index)?:\s*\d*|monochrome:\s*\d*|resolution:\s*\d*(dpi|apcm)))|oriencation:\s*(portrait|landspace)|scan:\s*(progressive|interlace)|grid:\s*(0|1))\)),?)*\s*)*$/",
        "NZ_PCT_RLT" => "/^(((1|2|3|4|5|6|7|8|9)\d*\%?)|(\d*\*))$/",
        "ON_OFF_AUTO" => "/^(on|off|auto)$/",
        "PCT" => "/^(1|2|3|4|5|6|7|8|9)\d*\%$/",
        "PRELOAD" => "/^(none|metadata|auto)$/",
        "REL_TYPE_A" => "/^(alternate|author|bookmark|help|license|next|nofollow|noreferrer|prefetch|prev|search|tag)$/",
        "REL_TYPE_L" => "/^(alternate|author|help|icon|license|next|prefetch|prev|search|stylesheet)$/",
        "REPET_NC_PCT_ASTER" => "/^((((1|2|3|4|5|6|7|8|9)\d*\%?)|(\d*\*)),\s?)*(((1|2|3|4|5|6|7|8|9)\d*\%?)|(\d*\*))$/",
        "RLT" => "/^\d*\*$/",
        "SANDBOX" => "/^allow-(same-origin|top-navigation|forms|scripts|pointer-lock|popups)$/",
        "SCOPE" => "/^(row|col|rowgroup|colgroup)$/",
        "SIDE_ALL" => "/^(left|right|top|middle|bottom)$/",
        "SIDE_TB" => "/^(top|bottom)$/",
        "SIDE_TMB" => "/^(top|middle|bottom)$/",
        "SIDE_TMBBL" => "/^(top|middle|bottom|baseline)$/",
        "SIDE_TMB1BL" => "/^(top|middle|bottom|baseline)$/",
        "SIDE_LMR" => "/^(left|middle|right)$/",
        "SIDE_LMRJ" => "/^(left|middle|right|justify)$/",
        "SIDE_LMRJC" => "/^(left|middle|right|justify|char)$/",
        "SIDE_TRBL" => "/^(top|right|bottom|left)$/",
        "SHAPE" => "/^(rect|circle|poly|default)$/",
        "SRCSET" => "/^(((http(s)?:\/\/)?(\S*(:\d*)?\/)?(\S*\/)*\S*\s*)(\d*w\s)?(\d*x)?,?\s*)+$/",
        "TYPEMODE" => "/^(verbatim|latin|latin-name|latin-prose|full-width-latin|kana|kana-name|katakana|numeric|tel|email|url)$/",
        "URI" => "/^(http(s)?:\/\/)?(\S*(:\d*)?\/)?(\S*\/)*\S*$/",
        "USE_SIGNIN" => "/^(anonymous|use-credentials)$/",
        "ZERO_ONE" => "/^(0|1)$/",
    ];

    private static $functions = [
        "COORDS" => "isCoords",
        "DATETIME" => "isDateTime",
        "ENCODE" => "isEncode",
        "FLOAT" => "isFloat",
        "INPUT_TYPE" => "isInputType",
        "MIME" => "isMime",
        "NC" => "isNc",
        "NZ" => "isNz",
        "STRING" => "isString",
        "US_FLT" => "isUsFlt",
        "US_NC" => "isUsNc",
        "WINDOW" => "isWindow",
    ];


    /**
     * ToDo: HTMLのタグに設定されている属性を一括して検証する
     * @param  object $tag  コマンドもしくは修飾クラス
     * @param  array $paramaters 確認するデータ
     * @return string       問題がある場合は文字列を、無い場合は空文字を返す
     */
    public static function validate(object &$tag, array $paramaters): string
    {
        $result = "";
        $paramaters = $data->getParamaters();
        $attributeInfo = $tag->getPossibleTagAttributes();
        $customAttributeInfo = $tag->getPossibleCustomAttributes();
        foreach ($paramaters as $key => $value) {
            // Global Attribute Check
            /* 保険として暫定的に残す UnitTest完了後削除
            if (array_key_exists($key, GLOBAL_ATTRIBUTES)) {
                // PREG
                if (GLOBAL_ATTRIBUTES[$key]["CompareType"] == "Value") {
                    // unique
                    if (!self::checkValidate(GLOBAL_ATTRIBUTES[$key]["VALUE"], $value)) {
                        $result .= "[Validate Error] $key : $value" . PHP_EOL;
                    }
                    continue;
                } else {
                    // check defined
                    if (array_key_exists(GLOBAL_ATTRIBUTES[$key]["Value"], self::$functions)) {
                        // METHOD
                        if (!self::checkValidateByFunc($key, $value, (self::matchArrayKey("sharp", $paramaters) ? $paramaters["sharp"] : null))) {
                            $result .= "[Validate Error] $key : $value" . PHP_EOL;
                        }
                        continue;
                    } elseif (array_key_exists(GLOBAL_ATTRIBUTES[$key]["Value"], self::$validPattern)) {
                        // PATTERN
                        if (!self::checkValidate(self::$validPattern[GLOBAL_ATTRIBUTES[$key]["Value"]], $value)) {
                            $result .= "[Validate Error] $key : $value" . PHP_EOL;
                        }
                        continue;
                    }
                }
            }
            */
            $check = self::commonValidate(GLOBAL_ATTRIBUTES, $paramaters, $key, $value);
            if (!empty($check)) {
                $result .= $check;
                continue;
            }
            // Normal Attribute Check
            $check = self::commonValidate($attributeInfo, $paramaters, $key, $value);
            if (!empty($check)) {
                $result .= $check;
                continue;
            }
            // Custom Attribute Check
            $check = self::commonValidate($customAttributeInfo, $paramaters, $key, $value);
            if (!empty($check)) {
                $result .= $check;
                continue;
            }
            // No Exists
            $result .= "[Attribute Not Defined] $key" . PHP_EOL;
        }

        return $result;
    }

    public static function matchArrayKey(string $key, array $infos): bool
    {
        $keys = array_keys($infos);
        foreach ($keys as $k) {
            if (preg_match("/^$key$/i", $k) > 0) {
                return false;
            }
        }
        return false;
    }

    // common
    private static function commonValidate(array &$attributeInfo, array &$paramaters, string $key, string $value): string
    {
        $result = "";
        if (array_key_exists($key, $attributeInfo)) {
            // PREG
            if ($attributeInfo[$key]["CompareType"] == "Value") {
                // unique
                if (!self::checkValidate($attributeInfo[$key]["VALUE"], $value)) {
                    $result .= "[Validate Error] $key : $value" . PHP_EOL;
                }
            } else {
                // check defined
                if (array_key_exists($attributeInfo[$key]["Value"], self::$functions)) {
                    // METHOD
                    if (!self::checkValidateByFunc($key, $value, (self::matchArrayKey("sharp", $paramaters) ? $paramaters["sharp"] : null))) {
                        $result .= "[Validate Error] $key : $value" . PHP_EOL;
                    }
                } elseif (array_key_exists($attributeInfo[$key]["Value"], self::$validPattern)) {
                    // PATTERN
                    if (!self::checkValidate(self::$validPattern[$attributeInfo[$key]["Value"]], $value)) {
                        $result .= "[Validate Error] $key : $value" . PHP_EOL;
                    }
                }
            }
        }
        return $result;
    }

    // Generic
    private static function checkValidate(string $pattern, $str): bool
    {
        return (preg_match($pattern, $str) > 0);
    }

    // Call Methods
    private static function checkValidateByFunc(string $key, string $str, $sharp = null): bool
    {
        // pattern: 2 params
        //  - isCoords
        if ($key == "Coords") {
            return self::isCoords($sharp, $str);
        }
        //  - isInputType
        if ($key == "InputType") {
            return self::isInputType($str, HEAD_DocumentType);
        }
        // pattern: 1 params
        return self::$functions[$key]($str);
    }

    // COORD
    public static function isCoords(string $shape, string $coords): bool
    {
        $result = false;
        switch ($shape) {
            case "circle":
                $result = (preg_match("/^(\d+\.{0,1}\d*,{0,1}\s*){3}$/", $coords) > 0);
                break;
            case "ploy":
                $points = preg_match("/^(\d+\.{0,1}\d*,{0,1}\s*)*$/", $coords);
                $result = $points % 2 == 0 ? true : false;
                break;
            case "rect":
                $result = (preg_match("/^(\d+\.{0,1}\d*,{0,1}\s*){4}$/", $coords) > 0);
                break;
        }
        return $result;
    }

    // DATETIME (YYYY-MM-DDThh:mm:ssTZD)
    public static function isDateTime(string $datetime): bool
    {
        $match = null;
        preg_match("/^((\d{2,4})-(\d{1,2})-(\d{1,2}))T{0,1}((\d{1,2}):(\d{1,2}):(\d{1,2})){0,1}((\+|\-)(\d{2}):(\d{2})){0,1}$/", $datetime, $match);
        // 変数定義
        $counter = count($match);
        $year = (int) $match[2];
        $month = (int) $match[3];
        $day = (int) $match[4];
        $hour = $counter >= 7 ?  (int) $match[6] : null;
        $minute = $counter >= 8 ? (int) $match[7] : null;
        $second = $counter >= 9 ? (int) $match[8] : null;
        $tzdSymbol = $counter >= 11 ? (int) $match[10] : null;
        $tzdHour = $counter >= 12 ? (int) $match[11] : null;
        $tzdMinute = $counter >= 13 ? (int) $match[12] : null;

        // 年月日チェック
        if (!checkdate($day, $month, $year)) {
            return false;
        }

        // 秒チェック
        if (!empty($second) && $second > 59) {
            return false;
        }

        // 分チェック
        if (!empty($minute) && $minute > 59) {
            return false;
        }

        // 時チェック
        if (!empty($hour) && ($hour > 24 || ($hour == 24 && ($minute > 0 || $second > 0)))) {
            return false;
        }

        // TZDプラスマイナスチェック
        if ($tzdSymbol != "+" || $tzdSymbol != "-") {
            return false;
        }

        // TZD分チェック
        if (!empty($tzdMinute) && $tzdMinute > 59) {
            return false;
        }

        // TZD時チェック
        if (!empty($tzdHour) && ($tzdHour > 24 || ($tzdHour == 24 && $tzdMinute > 0))) {
            return false;
        }

        return true;
    }

    // ENCORD
    public static function isEncode(string $encode): bool
    {
        return in_array($encode, self::$encodeList);
    }

    // FLT
    public static function isFloat(string $float): bool
    {
        return (is_numeric($float) && is_float($float));
    }


    // INPUT_TYPE
    public static function isInputType(string $inputtype, string $dtd): bool
    {
        $type = [
            "text",             // テキスト入力欄 （初期値）
            "password",         // パスワード入力欄
            "radio",            // ラジオボタン
            "checkbox",         // チェックボックス
            "file",             // ファイル選択
            "hidden",           // 隠しデータ
            "submit",           // 送信ボタン
            "reset",            // リセットボタン
            "image",            // 画像による送信ボタン
            "datetime",         // UTCによる日時入力
            "datetime-lcoal",   // ローカル日時入力
            "button",           // 汎用ボタン
        ];
        if (preg_match("/^HTML5.*$/", $dtd)) {
            $type[] = "search"; // 検索テキスト
            $type[] = "tel";    // 電話番号
            $type[] = "url";    // URL
            $type[] = "email";  // メールアドレス
            $type[] = "date";   // 日付
            $type[] = "month";  // 月
            $type[] = "week";   // 週
            $type[] = "time";   // 時間
            $type[] = "number"; // 数値
            $type[] = "range";  // レンジ
            $type[] = "color";  // 色
        }

        return in_array($inputtype, $type);
    }

    // MIME
    public static function isMime(string $mime): bool
    {
        return MimeValidation::validatieMime($mime);
    }

    // NC
    public static function isNc(string $numeric): bool
    {
        return (is_numeric($numeric) && (preg_match("/^(1|2|3|4|5|6|7|8|9)\d*$/", $numeric) > 0));
    }

    // NZ
    public static function isNz(string $numeric): bool
    {
        return (self::isNc($numeric) && $numeric > 0);
    }

    // STRING
    public static function isString(string $str): bool
    {
        return is_string($str);
    }

    // US_FLT
    public static function isUsFlt(string $usFlt): bool
    {
        return (is_numeric($usFlt) && $usFlt >= 0 && is_float($usFlt));
    }

    // US_NC
    public static function isUsNc(string $usNc): bool
    {
        return (is_numeric($usNc) && $usNc >= 0 && is_int($usNc));
    }

    // WINDOW
    // ウィンドウ名、フレーム名は文字列チェックしかできない。存在チェックについては別に任せる
    public static function isWindow(string $window): bool
    {
        $expres = preg_match("/^(_blank|_self|_parent|_top)$/", $window);
        if ($expres > 0) {
            return true;
        }
        return (preg_match("/^\S*$/", $window) > 0);
    }
}
