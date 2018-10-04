<?php
/**
 * parser.php
 *
 * @package Hasarius
 * @category utils
 * @author Takahiro Kotake
 * @license Teleios Development
 */

namespace Hasarius\utils;

use Hasarius\system\Vessel;

/**
 * 行解析ユーティリティ
 *
 * @package hasarius
 * @category utility
 * @author tkotake
 */
class Parser
{
    /**
     * 文字列を解析する
     * @param string $line
     * @param array  $subCommand
     * @param string $commandHead
     * @param string $parameterDelim
     * @param array $modifiersKey
     * @param string $escape
     * @return Vessel|null      サブコマンドの場合はnullを返し、それ以外はVesselオブジェクトを返す
     */
    public static function analyzeLine(
        array $line,
        array  $subCommand = [],
        string $commandHead = '#',
        string $parameterDelim = '=',
        array $modifiersKey = ['@', '@'],
        string $escape = '\\'
    ): Vessel {

        $vessel = new Vessel();

        // サブコマンドか確認
        if (!empty($subCommand)) {
            $subCommandPat = null;
            foreach ($subCommand as $scom) {
                $subCommandPat .= "|" . preg_quote($scom['Symbol']);
            }
            $subCommandPat = "/^\s*([" . ltrim($subCommandPat, "|") . "])\s*(.*)$/";
            $subCommandMatch = null;
            $flagSubCommantMatch = preg_match($subCommandPat, $line["lineText"], $subCommandMatch);
            if ($flagSubCommantMatch) {
                $vessel->setCommand($subCommandMatch[1]);
                $vessel->setSubCommand(true);
                $vessel->setText($subCommandMatch[2]);
                return $vessel;
            }
        }

        // 本文とコメントに分離
        $separated = self::separateComment($line["lineText"]);

        $lineWork = $separated['body'];
        $paramaters = [];
        $modifierCommand = [];
        $commandName = "";
        $text = rtrim($separated['body']);

        // コメント行か確認
        if (strlen(trim($separated['body'])) == 0 && mb_strlen($separated['comment']) > 0) {
            $vessel->setCommand(SYSTEM["COMMENT_LINE"]);
            $vessel->setComment($separated['comment']);
            return $vessel;
        }

        // 空白行か確認
        if (strlen(trim($separated['body'])) == 0 && mb_strlen($separated['comment']) == 0) {
            $vessel->setCommand(SYSTEM["EMPTY_LINE"]);
            return $vessel;
        }

        // ブロッククロースか確認
        $blockClose = $commandHead . $commandHead;
        if ($separated['body'] == $blockClose) {
            $vessel->setCommand(SYSTEM["BLOCK_CLOSE"]);
            $vessel->setComment($separated['comment']);
            return $vessel;
        }

        // 修飾コマンド抽出
        // 先に修飾コマンドの処理を入れて、本文中から抜いて処理をさせないと修飾コマンドの引数と、コマンドの引数がダブって処理される
        $modifierCommand = self::getModifiers($separated['body'], $modifiersKey, $escape);
        $withoutModifierCommand = self::getModifiers($separated['body'], $modifiersKey, $escape, true);
        $commandParamWork = str_replace($withoutModifierCommand, '', $separated['body']);

        // コマンドラインか確認
        $matchCommand = null;
        preg_match('/^' . $commandHead . '\S+\s*/ui', ltrim($commandParamWork), $matchCommand);
        $commandName = SYSTEM["TEXT_ONLY"];
        $paramaters = [];
        if (!empty($matchCommand)) {
            // コマンド確定
            $commandName = trim($matchCommand[0], $commandHead . ' ');
            $lineWork = str_replace($matchCommand[0], '', $commandParamWork);
            // パラメータ抽出
            $paramatersWork = self::getParamaters($lineWork, $parameterDelim);
            // テキスト抽出およびパラメータ解析
            $text = str_replace($matchCommand[0], '', $separated['body']);
            foreach ($paramatersWork as $param) {
                $text = str_replace($param, '', $text);
                // パラメータ解析
                list($key, $value) = explode($parameterDelim, trim($param, ' '));
                $paramaters[$key] = trim($value, '"');
            }
            $text = rtrim($text);
            // 本文から属性エスケープを除く
            $text = str_replace($escape . $parameterDelim, $parameterDelim, $text);
        }

        // テキスト中のエスケープを除く
        $excludeEscape = [$escape . $commandHead, $escape . $parameterDelim, $escape . $modifiersKey[0]];
        $replaceEscape = [$commandHead, $parameterDelim, $modifiersKey[0]];
        if ($modifiersKey[0] != $modifiersKey[1]) {
            $excludeEscape[] = $escape . $modifiersKey[1];
            $replaceEscape[] = $modifiersKey[1];
        }
        $text = str_replace($excludeEscape, $replaceEscape, $text);

        // データ設定
        $vessel->setCommand($commandName);
        $vessel->setParamaters($paramaters);
        $vessel->setModifiers($modifierCommand);
        //$vessel->setText(($commandName == "" ? $text : ltrim($text)));
        $vessel->setText($text);
        $vessel->setComment($separated['comment']);
        $vessel->setLineNumber($line["lineNumber"]);
        return $vessel;
    }

    /**
     * 処理対象行文字列を処理対象文字列とコメント文字列を分離
     * @param  string $str 処理対象行文字列
     * @return array       ['body' => 処理対象文字列, 'comment' => コメント文字列] コメントが無い場合には'comment'には空文字が入る
     */
    public static function separateComment(string $str): array
    {
        // 返り値初期化
        $result = [
            'body' => $str,             // コマンド指定無、本文のみ
            'comment' => "",
        ];
        // コメント行対応
        if (preg_match('/^\/\/\s*.*/', ltrim($str)) == 1) {
            $result['body'] = "";
            $result['comment'] = trim(preg_replace('/^\/\/\s*/', '', trim($str)));    // 本文中にコメントと同じ'//'を入れたい場合は'\/\/'とエスケープするが、実利用時に問題なるので置換しておく。
            return $result;
        }
        // 空白行対応
        $checkWhiteSpace = preg_replace('/(\s|　)/', '', $str);
        if (strlen($checkWhiteSpace) == 0) {
            $result['body'] = "";
            return $result;
        }

        // 通常コマンド対応
        $preg = '/(.*[^\s])\s*\/\/\s+(.*)/u';
        $matches = null;
        preg_match($preg, $str, $matches);
        if (count($matches) > 0) {
            $result['body']    = preg_replace('/\\\\\//', '/', $matches[1]);    // 本文中にコメントと同じ'//'を入れたい場合は'\/\/'とエスケープするが、実利用時に問題なるので置換しておく。
            $result['comment'] = ltrim($matches[2]);
        }
        return $result;
    }

    /**
     * 指定された文字列からパラメータを抽出する
     * 注意）属性値に関しては'"'で囲うこと!!
     *
     * @param string $line
     * @param string $parameterDelim
     * @param string $escape
     * @return array
     */
    private static function getParamaters(
        string $line,
        string $parameterDelim = '=',
        string $escape = '\\'
    ): array {
        $preg = '|.*[^\\\]' . $parameterDelim . '".*"|U';
        if ($escape != '\\') {
            $preg = '|.*[^' . $escape . ']' . $parameterDelim . '".*"|U';
        }
        $matches = [];
        preg_match_all($preg, $line, $matches, PREG_PATTERN_ORDER);
        return $matches[0];
    }

    /**
     * 指定された文字列から修飾コマンドを抽出する
     * @param string $line
     * @param array $modifiersKey
     * @param string $escape
     * @return array
     */
    private static function getModifiers(
        string $line,
        array $modifiersKey = ['@', '@'],
        string $escape = '\\',
        bool $withoutEscape = false
    ): array {
        $flagEscapeOn = false;
        $flagModifierOn = false;
        $modifiersCommand = "";
        $modifiers = [];
        $listChars = preg_split("//u", $line, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($listChars as $char) {
            // エスケープ中か？
            if ($flagEscapeOn) {
                // 修飾コマンド中なら修飾コマンド文字列中に含める？
                if ($flagModifierOn) {
                    $modifiersCommand .= $char;
                }
                $flagEscapeOn = false;
                continue;
            }
            // 文中のエスケープ文字か？
            if (!$withoutEscape && $char == $escape) {
                // 修飾コマンド中なら修飾コマンド文字列中に含める？
                if ($flagModifierOn) {
                    $modifiersCommand .= $char;
                }
                $flagEscapeOn = true;
                continue;
            }

            // 修飾コマンド開始か？
            if (!$flagModifierOn && $char == $modifiersKey[0]) {
                $modifiersCommand = "";
                $flagModifierOn = true;
                $modifiersCommand .= $char;
                continue;
            }
            // 修飾コマンド終了か？
            if ($flagModifierOn && $char == $modifiersKey[1]) {
                $modifiersCommand .= $char;
                $modifiers[] = $modifiersCommand;
                $flagModifierOn = false;
                continue;
            }
            // 修飾コマンド中か？
            if ($flagModifierOn) {
                $modifiersCommand .= $char;
            }
        }
        return $modifiers;
    }

    /**
     * 修飾コマンド解析
     * @param string $modifiers 修飾コマンド文字列
     * @param string $escape
     * @return array             解析結果を格納した連想配列
     *                           [
     *                              'command' => コマンド名文字列,
     *                              'params'  => 属性と属性値の文字列の配列
     *                              'text'    => 表示する文字列
     *                           ]
     */
    public static function analyzeModifier(string $modifiers, string $escape = '\\'): array
    {
        $inEscape = false;
        $inAttribute = 0;   // 0 ... 属性値以外, 1 ... 属性値待機('"'が来るまで待機), 2 ... 属性値入力
        $flagStarted = false;
        $command = "";
        $paramName = "";
        $flagCommandDone = false;
        $work = "";
        $params = [];
        $text = "";
        $listChars = preg_split("//u", $modifiers, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($listChars as $char) {
            if ($char == '@') {
                $flagStarted = true;
                continue;
            }
            if ($flagStarted) {
                if (!$flagCommandDone) {
                    if ($char == ' ') {
                        $flagCommandDone = true;
                        continue;
                    }
                    $command .= $char;
                } elseif (!$inEscape && $char == '@') {
                    // 修飾コマンド終了
                    $text = $work;
                    break;
                } else {
                    // エスケープ発生か？
                    if (!$inEscape && $char == $escape) {
                        $inEscape = true;
                        continue;
                    }
                    // 属性値関連
                    if (!$inEscape && $inAttribute == 0 && $char == "=") {
                        $paramName = trim($work);
                        $inAttribute = 1;
                        $work = "";
                        continue;
                    } elseif (!$inEscape && $char == '"') {
                        if ($inAttribute == 2) {
                            $params[$paramName] = trim($work);
                            $paramName = "";
                            $work = "";
                            $inAttribute = 0;
                            continue;
                        }
                        $work = "";
                        $inAttribute = 2;
                        continue;
                    }
                    // 文字確保
                    $work .= $char;
                    $inEscape = false;
                }
            }
        }

        $text = trim($work);
        $result = [
            'command' => $command,
            'params'  => $params,
            'text'    => $text,
        ];
        return $result;
    }

    /**
     * 引数で指定された文字列からファイルと子マントを取り出す
     * @param  string $source ファイル名。第２引数の指定が無い場合はフルパスで、第２引数を指定した場合は相対パスで指定する。
     * @param  string $baseDir ベースディレクトリ　第１引数で指定されているファイルを検索するためのベースディレクトリを指定
     * @return array          [
     *                          "filename" => <ファイル名>,      ファイル指定に該当した場合はファイル文字列、該当しない場合は null
     *                          "comment"  => <コメント文字列>   コメント指定に該当した場合はコメント文字列、該当しない場合は null
     *                        ]
     */
    public static function getIncludeFile(string $source, string $baseDir = null): array
    {
        $result = [
            "filename" => null,
            "comment" => null
        ];
        $match = null;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $source = preg_quote($source, '\\');
        }
        $flagMatch = preg_match('/^@include\s*(\S*)\s*(\/)*\s*(.*)$/u', $source, $match);
        if ($flagMatch) {
            $result["filename"] = trim($match[1], "\"'");
            $result["comment"]  = $match[3];
            if (preg_match('/^\/{2,}\s*.*$/u', trim($match[1]))) {
                // ファイル指定がない場合例外発生
                throw new \Exception("File is not mention !!");
            }
            if (!file_exists($result["filename"])) {
                if (!empty($baseDir) && file_exists($baseDir . DIRECTORY_SEPARATOR . $match[1])) {
                    $result["filename"] = $baseDir . DIRECTORY_SEPARATOR . $match[1];
                } else {
                    throw new \Exception("File not exists !! -> (" . $match[1] . ")");
                }
            }
        }
        return $result;
    }

    /**
     * 引数で指定された文字列が、変数定義である場合に、変数名、変数値、コメントに分解する
     * @param  string  $source 解析する文字列
     * @return array          [
     *                          "varName"  => <変数名>   変数定義である場合には変数名、そうでない場合は null
     *                          "varValue" => <変数値>   変数定義である場合には変数値、そうでない場合は null
     *                          "comment"  => <コメント> 変数定義でかつコメントが指定されている場合はコメント文字列、そうでない場合は null
     *                        ]
     * @throws Exception 定義方法が間違っている場合に例外が発生
     */
    public static function getValiable(string $source): array
    {
        $result = [
            "varName"  => null,
            "varValue" => null,
            "comment"  => null
        ];
        $pat = '/^@var\s*(.*)$/u';
        $match = null;
        $flagMatch = preg_match($pat, $source, $match);
        if ($flagMatch) {
            // 一度'='で分離する
            $tempArr = explode('=', $match[1]);
            $result["varName"] = trim(array_shift($tempArr));
            if (!empty($tempArr)) {
                // 変数名だけシフトしたあと'='で連結する
                $entity = implode('=', $tempArr);
                // 変数値とコメントを抽出する
                $valuePat = '/\/+/u';
                $valueMatch = preg_split($valuePat, $entity);
                // 変数値をセット
                $result["varValue"] = trim(array_shift($valueMatch));
                // 変数値がセットできない場合は例外発生
                if (empty($result["varValue"])) {
                    throw new \Exception("Valiable define error !! (Defined = '" . $source . "')");
                }
                // $entity から変数値を取り除く
                $tempComment = trim(str_replace($result["varValue"], '', $entity));
                // コメント開始文字列をその直後の半角空白文字を除き、コメントとしてセット
                $result['comment'] = \preg_replace('/\/*\s*/u', '', $tempComment);
            } else {
                // 変数値がセットできない場合は例外発生
                throw new \Exception("Valiable define error !! (Defined = '" . $source . "')");
            }
        }
        return $result;
    }

    /**
     * 配列データに存在する変数がテキストに存在する場合に置換処理を実施する
     * @param  array  $variables 変数データを格納した連想配列
     * @param  string $source    置換する対象の文字列
     * @return string            置換する変数があった場合には置換を行ったテキスト、なかった場合は元テキスト
     */
    public static function replaceVariable(array $variables, string $source): string
    {
        if (empty($variables)) {
            // $variableが空配列の場合は、置換処理をせずに元の文字列を返す
            return $source;
        }

        $search = [];
        $replace = [];
        foreach ($variables as $valName => $valInfo) {
            $search[]  = '#' . $valName . '#';
            $replace[] = trim($valInfo['varValue'], '"');
        }
        return str_replace($search, $replace, $source);
    }
}
