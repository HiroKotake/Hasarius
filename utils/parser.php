<?php
/**
 * parser.php
 *
 * @package hasarius
 * @category utility
 * @author Takahiro Kotake
 * @license Teleios Development
 */

namespace Hasarius\Utils;

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
     * @return array
     */
    public static function analyzeLine(
        string $line,
        string $commandHead = '#',
        string $parameterDelim = ':',
        array $modifiersKey = ['@', '@'],
        string $escape = '\\'
    ): array {
        $lineWork = $line;
        $paramaters = [];
        $modifierCommand = [];
        $commandName = "";
        $text = rtrim($line);

        // コマンドラインか確認
        $matchCommand = null;
        preg_match('|^' . $commandHead . '.*\s|U', $line, $matchCommand);
        if (!empty($matchCommand)) {
            // コマンド確定
            $commandName = trim($matchCommand[0], '# ');
            $lineWork = str_replace($matchCommand[0], '', $line);
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
        }

        // 修飾コマンド抽出
        $modifierCommand = self::getModifiers($text, $modifiersKey, $escape);

        return [
            'command' => $commandName,
            'paramaters' => $paramaters,
            'modifiers' => $modifierCommand,
            'text' => $text,
        ];
    }

    /**
     * 指定された文字列からパラメータを抽出する
     * @param string $line
     * @param string $parameterDelim
     * @param string $escape
     * @return array
     */
    private static function getParamaters(
        string $line,
        string $parameterDelim = ':',
        string $escape = '\\'
    ): array {
        if ($escape == '\\') {
            $preg = '|.*[^\\\]' . $parameterDelim . '.*\s|U';
        } else {
            $preg = '|.*[^' . $escape . ']' . $parameterDelim . '.*\s|U';
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
}
