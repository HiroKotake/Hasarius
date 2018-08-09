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
class Validation
{

    /**
     * エンコードリスト
     * ToDo: とりあえず日本語しか入っていないので、順次追加する必要がある。
     * @var array
     */
    private $encodeList = [
        'ISO-2022-JP',
        'UTF-8',
        'Shift_JIS',
        'EUC-JP'
    ];

    /**
     * ToDo: HTMLのタグに設定されている属性を一括して検証する
     * @param  object $tag  コマンドもしくは修飾クラス
     * @param  Vessel $data 確認するデータ
     * @return string       問題がある場合は文字列を、無い場合は空文字を返す
     */
    public static function validate(object &$tag, Vessel $data): string
    {
        $result = "";
        return $result;
    }

    // BUTTON_TYPE
    public static function isButtonType(string $data): bool
    {
        return (preg_match("/^(submit|reset|button)$/ui", $data) > 0);
    }

    // CLEAR_TYPE
    public static function isClearType(string $data): bool
    {
        return (preg_match("/^(left|right|all|none)$/ui", $data) > 0);
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

    // DIR_TYPE
    public static function isDirType(string $dirType): boot
    {
        return (preg_match("/^(ltr|rtl)$/", $dirType) > 0);
    }

    // ENCORD
    public static function isEncode(string $encode): bool
    {
        return in_array($encode, self::encodeList);
    }

    // FILENAME
    public static function isFilename(string $filename): bool
    {
        return (preg_match("/^\S*$/", $filename));
    }

    // FLT
    public static function isFloat(string $float): bool
    {
        return (is_numeric($float) && is_float($float));
    }

    // FONT
    // フォントリストを持たないので空白を含まない文字列であればとりあえずOKにしておく
    public static function isFont(string $font): bool
    {
        return (preg_match("/^.*$/", $font) > 0);
    }

    // GET_POST
    public static function isGetOrPost(string $str): bool
    {
        return (preg_match("/^(get|post)$/ui", $str) > 0);
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

    // LANG
    public static function isLang(string $lang): bool
    {
        return (preg_match("/^[a-z]{2}(-[a-z]{2})?$/", $lang) > 0);
    }

    // LINE_FRAME
    public static function isLineFrame(string $frame): bool
    {
        return (preg_match("/^(void|lhs|rhs|vsides|above|below|hsides|box|border)$/", $frame) > 0);
    }

    // LIST_NUM
    public static function isListNum(string $listNum): bool
    {
        return (preg_match("/^(1|A|a|I|i)$/", $listNum) > 0);
    }

    // LINE_RULES
    public static function isLineRules(string $lineRules): bool
    {
        return (preg_match("/^(none|rows|cols|groups|all)$/", $lineRules) > 0);
    }

    // LINE_TYPE
    public static function isLineType(string $lineType): bool
    {
        return (preg_match("/^(alternate|stylesheet|start|next|prev|contents|index|glossary|copyright|chapter|section|subsection|appendix|help|bookmark)$/", $lineType) > 0);
    }

    // LIST_STYLE
    public static function isListStyle(string $listStyle): bool
    {
        return (preg_match("/^(disc|circle|square|1|A|a|I|i)$/", $listStyle) > 0);
    }

    // LIST_SYMBOL
    public static function isListSymbol(string $listSymbol): bool
    {
        return (preg_match("/^(disc|circle|square)$/", $listSymbol) > 0);
    }

    // MEDIA_QUERY
    public static function isMediaQuery(string $mediaQuery): bool
    {
        return true;
        // ToDo: 正式な内容を書くこと
        return (preg_match("/^$/", $mediaQuery) > 0);
    }

    // MIME
    public static function isMime(string $mime): bool
    {
        return true;
        // ToDo: 正式な内容を書くこと
        return (preg_match("/^$/", $mime) > 0);
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

    // NZ_PCT
    public static function isNzPct(string $nzPct): bool
    {
        return (preg_match("/^(1|2|3|4|5|6|7|8|9)\d*\%?$/", $nzPct) > 0);
    }

    // NZ_PCT_RLT
    public static function isNzPctRlt(string $nzPctRlt): bool
    {
        return (preg_match("/^(((1|2|3|4|5|6|7|8|9)\d*\%?)|(\d*\*))$/", $nzPctRlt) > 0);
    }

    // ON_OFF_AUTO
    public static function isOnOffAuto(string $onOffAuto): bool
    {
        return (preg_match("/^(on|off|auto)$/", $onOffAuto) > 0);
    }

    // PCT
    public static function isPct(string $pct): bool
    {
        return (preg_match("/^(1|2|3|4|5|6|7|8|9)\d*\%$/", $pct) > 0);
    }

    // PRELOAD
    public static function isPreload(string $preload): bool
    {
        return (preg_match("/^(none|metadata|auto)$/", $preload) > 0);
    }

    // REL_TYPE_A
    public static function isRelTypeA(string $relTypeA): bool
    {
        return (preg_match("/^(alternate|author|bookmark|help|license|next|nofollow|noreferrer|prefetch|prev|search|tag)$/", $relTypeA) > 0);
    }

    // REL_TYPE_L
    public static function isRelTypeL(string $relTypeL): bool
    {
        return (preg_match("/^(alternate|author|help|icon|license|next|prefetch|prev|search|stylesheet)$/", $relTypeL) > 0);
    }

    // REPET_NC_PCT_ASTER
    public static function isRepetNcPctAster(string $repetNcPctAster): bool
    {
        return (preg_match("/^((((1|2|3|4|5|6|7|8|9)\d*\%?)|(\d*\*)),\s?)*(((1|2|3|4|5|6|7|8|9)\d*\%?)|(\d*\*))$/", $repetNcPctAster) > 0);
    }

    // RLT
    public static function isRlt(string $rlt): bool
    {
        return (preg_match("/^\d*\*$/", $rlt) > 0);
    }

    // SANDBOX
    public static function isSandbox(string $sandbox): bool
    {
        return (preg_match("/^allow-(same-origin|top-navigation|forms|scripts|pointer-lock|popups)$/", $sandbox) > 0);
    }

    // SCOPE
    public static function isScope(string $scope): bool
    {
        return (preg_match("/^(row|col|rowgroup|colgroup)$/", $scope) > 0);
    }

    // SIDE_ALL
    public static function isSideAll(string $sideAll): bool
    {
        return (preg_match("/^(left|right|top|middle|bottom)$/", $sideAll) > 0);
    }

    // SIDE_TB
    public static function isSideTb(string $sideTb): bool
    {
        return (preg_match("/^(top|bottom)$/", $sideTb) > 0);
    }

    // SIDE_TMB
    public static function isSideTmb(string $sideTmb): bool
    {
        return (preg_match("/^(top|middle|bottom)$/", $sideTmb) > 0);
    }

    // SIDE_TMBBL
    public static function isSideTmbbl(string $sideTmbbl): bool
    {
        return (preg_match("/^(top|middle|bottom|baseline)$/", $sideTmbbl) > 0);
    }

    // SIDE_TMB1BL
    public static function isSideTmb1bl(string $sideTmb1bl): bool
    {
        return (preg_match("/^(top|middle|bottom|baseline)$/", $sideTmb1bl) > 0);
    }

    // SIDE_LMR
    public static function isSideLmr(string $sideLmr): bool
    {
        return (preg_match("/^(left|middle|right)$/", $sideLmr) > 0);
    }

    // SIDE_LMRJ
    public static function isSideLmrj(string $sideLmrj): bool
    {
        return (preg_match("/^(left|middle|right|justify)$/", $sideLmrj) > 0);
    }

    // SIDE_LMRJC
    public static function isSideLmrjc(string $sideLmrjc): bool
    {
        return (preg_match("/^(left|middle|right|justify|char)$/", $sideLmrjc) > 0);
    }

    // SIDE_TRBL
    public static function isSideTrbl(string $sideTrbl): bool
    {
        return (preg_match("/^(top|right|bottom|left)$/", $sideTrbl) > 0);
    }

    // SHAPE
    public static function isShape(string $shape): bool
    {
        return (preg_match("/^(rect|circle|poly|default)$/", $shape) > 0);
    }

    // SRCSET
    public static function isSrcset(string $srcset): bool
    {
        return (preg_match("/^(((http(s)?:\/\/)?(\S*(:\d*)?\/)?(\S*\/)*\S*\s*)(\d*w\s)?(\d*x)?,?\s*)+$/", $srcset) > 0);
    }

    // STRING
    public static function isString(string $str): bool
    {
        return is_string($str);
    }

    // TYPEMODE
    public static function isTypemode(string $typemode): bool
    {
        return (preg_match("/^(verbatim|latin|latin-name|latin-prose|full-width-latin|kana|kana-name|katakana|numeric|tel|email|url)$/", $typemode) > 0);
    }

    // URI
    public static function isUri(string $uri): bool
    {
        return (preg_match("/^(http(s)?:\/\/)?(\S*(:\d*)?\/)?(\S*\/)*\S*$/", $uri) > 0);
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

    // ToDo: USE_SIGNIN
    public static function isUseSignin(string $useSignin): bool
    {
        return (preg_match("/^(anonymous|use-credentials)$/", $useSignin) > 0);
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

    // ZERO_ONE
    public static function isZeroOne(string $zeroOne): bool
    {
        return (preg_match("/^(0|1)$/", $zeroOne) > 0);
    }
}
