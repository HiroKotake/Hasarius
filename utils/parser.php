<?php
/**
 * parser.php
 *
 * @package hasarius
 * @category utility
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
     * @param string $commandHead
     * @param string $parameterDelim
     * @param array $modifiersKey
     * @param string $escape
     * @return Vessel
     */
    public static function analyzeLine(
        string $line,
        string $commandHead = '#',
        string $parameterDelim = '=',
        array $modifiersKey = ['@', '@'],
        string $escape = '\\'
    ): Vessel {
        // 本文とコメントに分離
        $separated = self::separateComment($line);

        $lineWork = $separated['body'];
        $paramaters = [];
        $modifierCommand = [];
        $commandName = "";
        $text = rtrim($separated['body']);

        // コマンドラインか確認
        $matchCommand = null;
        preg_match('/^' . $commandHead . '.*\s/U', $separated['body'], $matchCommand);
        if (!empty($matchCommand)) {
            // コマンド確定
            $commandName = trim($matchCommand[0], $commandHead . ' ');
            $lineWork = str_replace($matchCommand[0], '', $separated['body']);
            // パラメータ抽出
            $paramatersWork = self::getParamaters($lineWork, $parameterDelim);
            // テキスト抽出およびパラメータ解析
            $text = $lineWork;
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

        // 修飾コマンド抽出
        $modifierCommand = self::getModifiers($text, $modifiersKey, $escape);
        $vessel = new Vessel();
        $vessel->setCommand($commandName);
        $vessel->setParamaters($paramaters);
        $vessel->setModifiers($modifierCommand);
        $vessel->setText(($commandName == "" ? $text : ltrim($text)));
        $vessel->setComment($separated['comment']);
        return $vessel;
    }

    /**
     * 処理対象行文字列を処理対象文字列とコメント文字列を分離
     * @param  string $str 処理対象行文字列
     * @return array       ['body' => 処理対象文字列, 'comment' => コメント文字列] コメントが無い場合には'comment'には空文字が入る
     */
    public function separateComment(string $str): array
    {
        $preg = '|(.*[^\s])\s*\/\/s*(.*)|u';
        $matches = null;
        preg_match($preg, $str, $matches);
        $result = [
            'body' => $str,
            'comment' => "",
        ];
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
        string $escape = '\\'
    ): array {
        $flagEscapeOn = false;
        $flagModifierOn = false;
        $length = mb_strlen($line);
        $modifiersCommand = "";
        $modifiers = [];

        for ($i = 0; $i < $length; $i++) {
            // エスケープ中か？
            if ($flagEscapeOn) {
                // 修飾コマンド中なら修飾コマンド文字列中に含める？
                if ($flagModifierOn) {
                    $modifiersCommand .= $line[$i];
                }
                $flagEscapeOn = false;
                continue;
            }
            // 文中のエスケープ文字か？
            if ($line[$i] == $escape) {
                // 修飾コマンド中なら修飾コマンド文字列中に含める？
                if ($flagModifierOn) {
                    $modifiersCommand .= $line[$i];
                }
                $flagEscapeOn = true;
                continue;
            }

            // 修飾コマンド開始か？
            if (!$flagModifierOn && $line[$i] == $modifiersKey[0]) {
                $modifiersCommand = "";
                $flagModifierOn = true;
                $modifiersCommand .= $line[$i];
                continue;
            }
            // 修飾コマンド終了か？
            if ($flagModifierOn && $line[$i] == $modifiersKey[1]) {
                $modifiersCommand .= $line[$i];
                $modifiers[] = $modifiersCommand;
                $flagModifierOn = false;
                continue;
            }
            // 修飾コマンド中か？
            if ($flagModifierOn) {
                $modifiersCommand .= $line[$i];
            }
        }
        return $modifiers;
    }

    /**
     * 修飾コマンド解析
     * @param  string $modifiers 修飾コマンド文字列
     * @return array             解析結果を格納した連想配列
     *                           [
     *                              'command' => コマンド名文字列,
     *                              'params'  => 属性と属性値の文字列の配列
     *                              'text'    => 表示する文字列
     *                           ]
     */
    public static function analyzeModifier(string $modifiers): array
    {
        $pattern = '/^@(\S+)\s((\S+=\S+\s)*)(.*)@$/u';
        $matches = null;
        preg_match_all($pattern, $modifiers, $matches);
        $command = $matches[1][0];
        $paramString = $matches[2][0];
        $paramMatches = null;
        $params = null;
        if (!empty($paramString)) {
            preg_match_all('/\S+=\S+/u', $paramString, $paramMatches);
            $params = [];
            foreach ($paramMatches[0] as $attribute) {
                list($key, $value) = explode("=", $attribute);
                $value = str_replace("_", " ", trim($value, "\"'"));
                $params[$key] = $value;
            }
        }
        $text = array_pop($matches);
        $text = $text[0];
        return [
            'command' => $command,
            'params'  => $params,
            'text'    => $text,
        ];
    }
}
