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
        "BUTTON_TYPE" => "/^(submit|reset|button)$/ui",
        "CLEAR_TYPE" => "/^(left|right|all|none)$/ui",
        "DIR_TYPE" => "/^(ltr|rtl|auto)$/ui",
        "FONT" => "/^.*$/",     // フォントリストを持たないので空白を含まない文字列であればとりあえずOKにしておく
        "GET_POST" => "/^(get|post)$/ui",
        "INPUT_MODE" => "/^(verbatim|latin|latin-name|latin-prose|full-width-latin|kana|kana-name|katakana|numeric|tel|email|url)$/ui",
        "LANG" => "/^[a-z]{2}(-[a-zA-Z]{2})?$/",
        "LINE_FRAME" => "/^(void|lhs|rhs|vsides|above|below|hsides|box|border)$/ui",
        "LIST_NUM" => "/^(1|A|a|I|i)$/",
        "LINE_RULES" => "/^(none|rows|cols|groups|all)$/ui",
        "LINE_TYPE" => "/^(alternate|stylesheet|start|next|prev|contents|index|glossary|copyright|chapter|section|subsection|appendix|help|bookmark)$/ui",
        "LIST_STYLE" => "/^(disc|circle|square|1|A|a|I|i)$/",
        "LIST_SYMBOL" => "/^(disc|circle|square)$/ui",
        "NC_PCT" => "/^-?\d*\%?$/",
        "NZ_PCT" => "/^-?(1|2|3|4|5|6|7|8|9)\d*\%?$/",
        "MEDIA_QUERY" => "/^(not|only)?\s*((all|screen|print|speech|tv|projection|handheld|tty|braille|embossed)?\s*((and)?\s*(\((((min-|max-)?((device-)?width:\s*\d*px|(device-)?height:\s*\d*|(device-)?acept-ratio:\s*(\d*\/\d*)|color(-index)?:\s*\d*|monochrome:\s*\d*|resolution:\s*\d*(dpi|apcm)))|oriencation:\s*(portrait|landspace)|scan:\s*(progressive|interlace)|grid:\s*(0|1))\))*(\s*\d*(vw|px))?,?)*\s*)*$/",
        "NZ_PCT_RLT" => "/^(((1|2|3|4|5|6|7|8|9)\d*\%?)|(\d*\*))$/",
        "ON_OFF" => "/^(on|off)$/ui",
        "ON_OFF_AUTO" => "/^(on|off|auto)$/ui",
        "PCT" => "/^(1|2|3|4|5|6|7|8|9)\d*\%$/",
        "PRELOAD" => "/^(none|metadata|auto)$/ui",
        "REL_TYPE_A" => "/^(alternate|author|bookmark|help|license|next|nofollow|noreferrer|prefetch|prev|search|tag)$/ui",
        "REL_TYPE_L" => "/^(alternate|author|help|icon|license|next|prefetch|prev|search|stylesheet)$/ui",
        "REPET_NC_PCT_ASTER" => "/^((((1|2|3|4|5|6|7|8|9)\d*\%?)|(\d*\*)),\s?)*(((1|2|3|4|5|6|7|8|9)\d*\%?)|(\d*\*))$/",
        "RLT" => "/^\d*\*$/",
        "SANDBOX" => "/^allow-(same-origin|top-navigation|forms|scripts|pointer-lock|popups)$/ui",
        "SCOPE" => "/^(row|col|rowgroup|colgroup)$/ui",
        "SIDE_ALL" => "/^(left|right|top|middle|bottom)$/ui",
        "SIDE_TB" => "/^(top|bottom)$/ui",
        "SIDE_TMB" => "/^(top|middle|bottom)$/ui",
        "SIDE_TMBBL" => "/^(top|middle|bottom|baseline)$/ui",
        "SIDE_TMB1BL" => "/^(top|middle|bottom|baseline)$/ui",
        "SIDE_LMR" => "/^(left|middle|right)$/ui",
        "SIDE_LMRJ" => "/^(left|middle|right|justify)$/ui",
        "SIDE_LMRJC" => "/^(left|middle|right|justify|char)$/ui",
        "SIDE_TRBL" => "/^(top|right|bottom|left)$/ui",
        "SHAPE" => "/^(rect|circle|poly|default)$/ui",
        "SRCSET" => "/^(http(s)?:\/\/)?\S+(\s+\d+(w|x)?)?(,\s+(http(s)?:\/\/)?\S+(\s+\d+(w|x)?)?)*$/",
        "TYPEMODE" => "/^(verbatim|latin|latin-name|latin-prose|full-width-latin|kana|kana-name|katakana|numeric|tel|email|url)$/ui",
        //"URI" => "/^(http(s)?:\/\/)?([\w-]*\.{0,2})+(\/[\w- .\/?%&=]*)?$/",
        "URI" => "/^((http(s)?:\/\/)?([\w-]*\.{0,2})+(\/[\w-.\/?%&=]*)|#\S*)?$/",
        "USE_SIGNIN" => "/^(anonymous|use-credentials)$/ui",
        "US_NC_PCT" => "/^\d*\%?$/",
        "US_NZ_PCT" => "/^(1|2|3|4|5|6|7|8|9)\d*\%?$/",
        "ZERO_ONE" => "/^(0|1)$/",
    ];

    private static $functions = [
        "ANY_US_NZ" => "isAnyUsNz",
        "COLOR" => "isColor",
        "COORDS" => "isCoords",
        "DATETIME" => "isDateTime",
        "ENCODE" => "isEncode",
        "FILENAME" => "isString",
        "FLOAT" => "isFloat",
        "FLT" => "isFloat",
        "INPUT_TYPE" => "isInputType",
        "MIME" => "isMime",
        "NC" => "isNc",
        "NZ" => "isNz",
        "ONE_NULL" => "isOneNull",
        "STRING" => "isString",
        "US_FLT" => "isUsFlt",
        "US_NC" => "isUsNc",
        "US_NZ" => "isUsNz",
        "WINDOW" => "isWindow",
    ];

    private static $colorChart = [
        "a" => [
                    "aliceblue",
                    "antiquewhite",
                    "aqua",
                    "aquamarine",
                    "azure",
               ],
        "b" => [
                    "beige",
                    "bisque",
                    "black",
                    "blanchedalmond",
                    "blue",
                    "blueviolet",
                    "brass",
                    "brown",
                    "burlywood",
               ],
        "c" => [
                    "cadetblue",
                    "chartreuse",
                    "chocolate",
                    "coolcopper",
                    "copper",
                    "coral",
                    "cornflower",
                    "cornflowerblue",
                    "cornsilk",
                    "crimson",
                    "cyan",
               ],
        "d" => [
                    "darkblue",
                    "darkbrown",
                    "darkcyan",
                    "darkgoldenrod",
                    "darkgray",
                    "darkgreen",
                    "darkkhaki",
                    "darkmagenta",
                    "darkolivegreen",
                    "darkorange",
                    "darkorchid",
                    "darkred",
                    "darksalmon",
                    "darkseagreen",
                    "darkslateblue",
                    "darkslategray",
                    "darkturquoise",
                    "darkviolet",
                    "deeppink",
                    "deepskyblue",
                    "dimgray",
                    "dodgerblue",
               ],
        "f" => [
                    "feldsper",
                    "firebrick",
                    "floralwhite",
                    "forestgreen",
                    "fuchsia",
               ],
        "g" => [
                    "gainsboro",
                    "ghostwhite",
                    "gold",
                    "goldenrod",
                    "gray",
                    "green",
                    "greenyellow",
               ],
        "h" => [
                    "honeydew",
                    "hotpink",
               ],
        "i" => [
                    "indianred",
                    "indigo",
                    "ivory",
               ],
        "k" => [
                    "khaki",
               ],
        "l" => [
                    "lavender",
                    "lavenderblush",
                    "lawngreen",
                    "lemonchiffon",
                    "lightblue",
                    "lightcoral",
                    "lightcyan",
                    "lightgoldenrodyellow",
                    "lightgreen",
                    "lightgrey",
                    "lightpink",
                    "lightsalmon",
                    "lightseagreen",
                    "lightskyblue",
                    "lightslategray",
                    "lightsteelblue",
                    "lightyellow",
                    "lime",
                    "limegreen",
                    "linen",
               ],
        "m" => [
                    "magenta",
                    "maroon",
                    "mediumaquamarine",
                    "mediumblue",
                    "mediumorchid",
                    "mediumpurple",
                    "mediumseagreen",
                    "mediumslateblue",
                    "mediumspringgreen",
                    "mediumturquoise",
                    "mediumvioletred",
                    "midnightblue",
                    "mintcream",
                    "mistyrose",
                    "moccasin",
               ],
        "n" => [
                    "navajowhite",
                    "navy",
               ],
        "o" => [
                    "oldlace",
                    "olive",
                    "olivedrab",
                    "orange",
                    "orangered",
                    "orchid",
               ],
        "p" => [
                    "palegoldenrod",
                    "palegreen",
                    "paleturquoise",
                    "palevioletred",
                    "papayawhip",
                    "peachpuff",
                    "peru",
                    "pink",
                    "plum",
                    "powderblue",
                    "purple",
               ],
        "r" => [
                    "red",
                    "richblue",
                    "rosybrown",
                    "royalblue",
               ],
        "s" => [
                    "saddlebrown",
                    "salmon",
                    "sandybrown",
                    "seagreen",
                    "seashell",
                    "sienna",
                    "silver",
                    "skyblue",
                    "slateblue",
                    "slategray",
                    "snow",
                    "springgreen",
                    "steelblue",
               ],
        "t" => [
                    "tan",
                    "teal",
                    "thistle",
                    "tomato",
                    "turquoise",
               ],
        "v" => [
                    "violet",
               ],
        "w" => [
                    "wheat",
                    "white",
                    "whitesmoke",
               ],
        "y" => [
                    "yellow",
                    "yellowgreen",
               ],
    ];


    /**
     * ToDo: HTMLのタグに設定されている属性を一括して検証する
     * @param  object $tag  コマンドもしくは修飾クラス
     * @param  array $paramaters 確認するデータ
     * @return string       問題がある場合は文字列を、無い場合は空文字を返す
     */
    public static function validate(object &$tag, array $paramaters): array
    {
        $result = [];
        $attributeInfo = $tag->getPossibleTagAttributes();
        $customAttributeInfo = $tag->getPossibleCustomAttributes();
        $eventAttributeInfo = $tag->getPossibleEventAttributes();
        foreach ($paramaters as $key => $value) {
            // Hasarius Common Check
            if (preg_match("/^(ScriptFile|CssFile)$/ui", $key) > 0) {
                if (!file_exists($value)) {
                    $result[] = "[Validate Error] File is not exists. ($key : $value)" . PHP_EOL;
                }
                continue;
            }
            // Global Attribute Check
            if (array_key_exists($key, GLOBAL_ATTRIBUTES)) {
                // PREG
                if (GLOBAL_ATTRIBUTES[$key]["CompareType"] == "NONE") {
                    continue;
                } elseif (GLOBAL_ATTRIBUTES[$key]["CompareType"] == "VALUE") {
                    // unique
                    if (!self::checkValidate(GLOBAL_ATTRIBUTES[$key]["Value"], $value)) {
                        $result[] = "[Validate Error] $key : $value" . PHP_EOL;
                    }
                    continue;
                } else {
                    // check defined
                    if (array_key_exists(GLOBAL_ATTRIBUTES[$key]["Value"], self::$functions)) {
                        // METHOD
                        if (!self::checkValidateByFunc(GLOBAL_ATTRIBUTES[$key]["Value"], $value, (self::matchArrayKey("shape", $paramaters) ? $paramaters["shape"] : null))) {
                            $result[] = "[Validate Error] $key : $value" . PHP_EOL;
                        }
                        continue;
                    } elseif (array_key_exists(GLOBAL_ATTRIBUTES[$key]["Value"], self::$validPattern)) {
                        // PATTERN
                        if (!self::checkValidate(self::$validPattern[GLOBAL_ATTRIBUTES[$key]["Value"]], $value)) {
                            $result[] = "[Validate Error] $key : $value" . PHP_EOL;
                        }
                        continue;
                    }
                }
            }
            // Event Attribute Check
            if (array_key_exists(MAKE_DocumentType, $eventAttributeInfo) && in_array($key, $eventAttributeInfo[MAKE_DocumentType])) {
                // イベント関連の属性ならば属性値は javascript なのでチェックせずに次へ
                continue;
            }
            // Normal Attribute Check
            $check = self::commonValidate($attributeInfo[MAKE_DocumentType], $paramaters, $key, $value);
            if ($check["existence"]) {
                if (!empty($check["message"])) {
                    $result[] = $check["message"];
                }
                continue;
            }
            // Custom Attribute Check
            if (array_key_exists($key, $customAttributeInfo) && in_array(MAKE_DocumentType, $customAttributeInfo[$key]["DocumentType"])) {
                $check = self::commonValidate($customAttributeInfo, $paramaters, $key, $value);
                if ($check["existence"]) {
                    if (!empty($check["message"])) {
                        $result[] = $check["message"];
                    }
                    continue;
                }
            }
            // No Exists
            $result[] = "[Attribute Not Defined] $key" . PHP_EOL;
        }
        return $result;
    }

    public static function matchArrayKey(string $key, array $infos): bool
    {
        $keys = array_keys($infos);
        foreach ($keys as $k) {
            if (preg_match("/^$key$/i", $k) > 0) {
                return true;
            }
        }
        return false;
    }

    // common
    private static function commonValidate(array &$attributeInfo, array &$paramaters, string $key, string $value): array
    {
        $result = ["existence" => false, "message" => ""];
        if (array_key_exists($key, $attributeInfo)) {
            $result["existence"] = true;
            // PREG
            if ($attributeInfo[$key]["CompareType"] == "NONE") {
                return $result;
            } elseif ($attributeInfo[$key]["CompareType"] == "VALUE") {
                // unique
                if (!self::checkValidate($attributeInfo[$key]["Value"], $value)) {
                    $result["message"] .= "[Validate Error] $key : $value" . PHP_EOL;
                }
            } else {
                // check defined
                if (array_key_exists($attributeInfo[$key]["Value"], self::$functions)) {
                    // METHOD
                    if (!self::checkValidateByFunc($attributeInfo[$key]["Value"], $value, (self::matchArrayKey("shape", $paramaters) ? $paramaters["shape"] : null))) {
                        $result["message"] .= "[Validate Error] $key : $value" . PHP_EOL;
                    }
                } elseif (array_key_exists($attributeInfo[$key]["Value"], self::$validPattern)) {
                    // PATTERN
                    if (!self::checkValidate(self::$validPattern[$attributeInfo[$key]["Value"]], $value)) {
                        $result["message"] .= "[Validate Error] $key : $value" . PHP_EOL;
                    }
                } else {
                    $result["existence"] = false;
                }
            }
        }
        return $result;
    }

    // Generic
    private static function checkValidate(string $pattern, $str): bool
    {
        return (preg_match($pattern, $str) != 0);
    }

    // Call Methods
    private static function checkValidateByFunc(string $key, string $str, $shape = null): bool
    {
        // pattern: 2 params
        //  - isCoords
        if ($key == "COORDS") {
            return self::isCoords($shape, $str);
        }
        //  - isInputType
        if ($key == "INPUT_TYPE") {
            return self::isInputType($str, MAKE_DocumentType);
        }
        // pattern: 1 params
        $func = self::$functions[$key];
        return self::$func($str);
    }

    private static function inRange(int $base, int $start, int $end): bool
    {
        return ($start <= $base && $base <= $end);
    }

    // COLOR
    public static function isColor(string $color): bool
    {
        // color chart
        $colorName = strtolower($color);
        $firstChar = substr($colorName, 0, 1);
        if (array_key_exists($firstChar, self::$colorChart) && in_array($colorName, self::$colorChart[$firstChar])) {
            return true;
        }

        // 16: hexadecimal
        $preg = "/^#([AaBbCcDdEeFf0-9]{3}|[AaBbCcDdEeFf0-9]{6}|[AaBbCcDdEeFf0-9]{8})$/";
        if (preg_match($preg, $color) >= 1) {
            return true;
        }

        // For Style
        // %: percent
        $clrs = explode(",", $color);
        if (count($clrs) < 3) {
            return false;
        }
        $clrs[0] = trim($clrs[0]);
        $clrs[1] = trim($clrs[1]);
        $clrs[2] = trim($clrs[2]);
        if (count($clrs) == 3) {
            $color0 = (preg_match("/^\d{1,3}%$/", $clrs[0]) == 1 && self::inRange($clrs[0], 0, 100));
            $color1 = (preg_match("/^\d{1,3}%$/", $clrs[1]) == 1 && self::inRange($clrs[1], 0, 100));
            $color2 = (preg_match("/^\d{1,3}%$/", $clrs[2]) == 1 && self::inRange($clrs[2], 0, 100));
            if ($color0 && $color1 && $color2) {
                return true;
            }
        } elseif (count($clrs) == 4) {
            $clrs[3] = trim($clrs[3]);
            $color0 = (preg_match("/^\d{1,3}%$/", $clrs[0]) == 1 && self::inRange($clrs[0], 0, 100));
            $color1 = (preg_match("/^\d{1,3}%$/", $clrs[1]) == 1 && self::inRange($clrs[1], 0, 100));
            $color2 = (preg_match("/^\d{1,3}%$/", $clrs[2]) == 1 && self::inRange($clrs[2], 0, 100));
            $color3 = (preg_match("/^\d{1,3}%$/", $clrs[3]) == 1 && self::inRange($clrs[3], 0, 100));
            if ($color0 && $color1 && $color2 && $color3) {
                return true;
            }
        }

        // 10: decimal
        if (count($clrs) == 3) {
            if (self::inRange($clrs[0], 0, 255) && self::inRange($clrs[1], 0, 255) && self::inRange($clrs[2], 0, 255)) {
                return true;
            }
        } elseif (count($clrs) == 4) {
            if (self::inRange($clrs[0], 0, 255) && self::inRange($clrs[1], 0, 255) && self::inRange($clrs[2], 0, 255) && self::inRange($clrs[3], 0, 100)) {
                return true;
            }
        }

        // No Match
        return false;
    }

    // COORD
    public static function isCoords(string $shape, $coords): bool
    {
        $result = false;
        $match = explode(",", $coords);
        foreach ($match as $numb) {
            if (!self::isDecimalNumber($numb)) {
                return false;
            }
        }
        $points = count($match);
        switch ($shape) {
            case "circle":
                $result = $points == 3 ? true : false;
                break;
            case "poly":
                $result = $points % 2 == 0 ? true : false;
                break;
            case "rect":
                $result = $points == 4 ? true : false;
                break;
        }
        return $result;
    }

    // DATETIME (YYYY-MM-DDThh:mm:ssTZD)
    public static function isDateTime(string $datetime): bool
    {
        if (strlen($datetime) <= 0) {
            return false;
        }

        $match = null;
        $matchResult = preg_match("/^(\d{4})-((0|1)\d)-((0|1|2|3)\d)T((0|1|2)\d):((0|1|2|3|4|5|)\d):((0|1|2|3|4|5|)\d)(Z|(\+|\-)((0|1|2)\d):((0|1|2|3|4|5|)\d))$/", $datetime, $match);
        if ($matchResult == 0) {
            return false;
        }

        // 変数定義
        $counter   = count($match);
        $year      = (int) $match[1];
        $month     = (int) $match[2];
        $day       = (int) $match[4];
        $hour      = (int) $match[6];
        $minute    = (int) $match[8];
        $second    = (int) $match[10];
        if ($counter > 14) {
            $tzdSymbol = $match[13];
            $tzdHour   = (int) $match[14];
            $tzdMinute = (int) $match[16];
        } else {
            $tzdSymbol = $match[12];
        }

        // 年月日チェック
        if (!checkdate((int)$month, (int)$day, (int)$year)) {
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
        //if ($tzdSymbol != "+" || $tzdSymbol != "-" || $tzdSymbol != "Z") {
        if (preg_match("/^(\+|-|Z)$/", $tzdSymbol) == 0) {
            return false;
        }

        if ($counter > 14) {
            // TZD分チェック
            if (!empty($tzdMinute) && $tzdMinute > 59) {
                return false;
            }

            // TZD時チェック
            if (!empty($tzdHour) && ($tzdHour > 24 || ($tzdHour == 24 && $tzdMinute > 0))) {
                return false;
            }
        }

        return true;
    }

    // ENCORD
    public static function isEncode(string $encode): bool
    {
        return empty($encode) ? false : in_array($encode, self::$encodeList);
    }

    // FLT
    public static function isFloat(string $float): bool
    {
        if (empty($float)) {
            return false;
        }
        return (is_numeric($float) && is_float((float) $float));
    }


    // INPUT_TYPE
    public static function isInputType(string $inputtype, string $dtd): bool
    {
        if (empty($inputtype) || empty($dtd)) {
            return false;
        }

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
            "button",           // 汎用ボタン
        ];
        if (preg_match("/^HTML5.*$/", $dtd)) {
            $type[] = "search";         // 検索テキスト
            $type[] = "tel";            // 電話番号
            $type[] = "url";            // URL
            $type[] = "email";          // メールアドレス
            $type[] = "date";           // 日付
            $type[] = "month";          // 月
            $type[] = "week";           // 週
            $type[] = "time";           // 時間
            $type[] = "datetime-local"; // ローカル日時入力
            $type[] = "number";         // 数値
            $type[] = "range";          // レンジ
            $type[] = "color";          // 色
        }

        return in_array($inputtype, $type);
    }

    // MIME
    public static function isMime(string $mime): bool
    {
        return empty($mime) ? false : MimeValidation::validatieMime($mime);
    }

    // NC
    public static function isNc(string $numeric): bool
    {
        if (empty($numeric)) {
            return false;
        }
        return (is_numeric($numeric) && (preg_match("/^-?(1|2|3|4|5|6|7|8|9)\d*$/", $numeric) > 0));
    }

    // NZ
    public static function isNz(string $numeric): bool
    {
        if (empty($numeric)) {
            return false;
        }
        return (self::isNc($numeric) && $numeric > 0);
    }

    // ONE_NULL
    public static function isOneNull(string $str): bool
    {
        return (empty($str) || $str == 1);
    }

    // STRING
    public static function isString(string $str): bool
    {
        return (empty($str) || self::isWhiteSpaces($str)) ? false : is_string($str);
    }

    // US_FLT
    public static function isUsFlt(string $usFlt): bool
    {
        if (empty($usFlt)) {
            return false;
        }
        return (is_numeric($usFlt) && (float) $usFlt >= 0 && is_float((float) $usFlt));
    }

    // US_NC
    public static function isUsNc(string $usNc): bool
    {
        if (empty($usNc)) {
            return false;
        }
        return (is_numeric($usNc) && $usNc >= 0 && is_int($usNc));
    }

    // US_NZ
    public static function isUsNz(string $usNz): bool
    {
        if (empty($usNz)) {
            return false;
        }
        return (self::isDecimalNumber($usNz) && $usNz > 0 && is_int((int) $usNz));
    }

    // WINDOW
    // ウィンドウ名、フレーム名は文字列チェックしかできない。存在チェックについては別に任せる
    public static function isWindow(string $window): bool
    {
        if (empty($window)) {
            return false;
        }
        $expres = preg_match("/^(_blank|_self|_parent|_top)$/", $window);
        if ($expres > 0) {
            return true;
        }
        return (preg_match("/^\S*$/", $window) > 0);
    }

    public static function isDecimalNumber($numb): bool
    {
        return preg_match('/^-?\d*$/', $numb) == 1;
    }

    public static function isFloatNumber($numb): bool
    {
        return preg_match('/^-?\d*\.?\d*$/', $numb) == 1;
    }

    public static function isWhiteSpaces($str): bool
    {
        return preg_match('/^[\s　]$/', $str) == 1;
    }

    public static function isAnyUsNz(string $str): bool
    {
        if (preg_match("/^any$/ui", $str) == 1) {
            return true;
        }

        return self::isUsNc($str);
    }
}
