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
     * @param string $command_head
     * @param string $parameter_delim
     * @param array $modifiers_key
     * @param string $escape
     * @return array
     */
    public static function analyzeLine(
        string $line,
        string $command_head = '#',
        string $parameter_delim = ':',
        array $modifiers_key = ['@', '@'],
        string $escape = '\\'
    ): array {
        $line_work = $line;
        $paramaters = [];
        $modifier_command = [];
        $command_name = "";
        $text = rtrim($line);

        // コマンドラインか確認
        $match_command = null;
        preg_match('|^#.*\s|U', $line, $match_command);
        if (!empty($match_command)) {
            // コマンド確定
            $command_name = trim($match_command[0], '# ');
            $line_work = str_replace($match_command[0], '', $line);
            // パラメータ抽出
            $paramaters_work = self::getParamaters($line_work, $parameter_delim);
            // テキスト抽出およびパラメータ解析
            $text = $line_work;
            foreach ($paramaters_work as $param) {
                $text = str_replace($param, '', $text);
                // パラメータ解析
                list($key, $value) = explode($parameter_delim, trim($param, ' '));
                $paramaters[$key] = trim($value, '"');
            }
            $text = rtrim($text);
        }

        // 修飾コマンド抽出
        $modifier_command = self::getModifiers($text, $modifiers_key, $escape);

        return [
            'command' => $command_name,
            'paramaters' => $paramaters,
            'modifiers' => $modifier_command,
            'text' => $text,
        ];
    }

    /**
     * 指定された文字列からパラメータを抽出する
     * @param string $line
     * @param string $parameter_delim
     * @param string $escape
     * @return array
     */
    private static function getParamaters(
        string $line,
        string $parameter_delim = ':',
        string $escape = '\\'
    ): array {
        if ($escape == '\\') {
            $preg = '|.*[^\\\]' . $parameter_delim . '.*\s|U';
        } else {
            $preg = '|.*[^' . $escape . ']' . $parameter_delim . '.*\s|U';
        }
        $matches = [];
        preg_match_all($preg, $line, $matches, PREG_PATTERN_ORDER);
        return $matches[0];
    }

    /**
     * 指定された文字列から修飾コマンドを抽出する
     * @param string $line
     * @param array $modifiers_key
     * @param string $escape
     * @return array
     */
    private static function getModifiers(
        string $line,
        array $modifiers_key = ['@', '@'],
        string $escape = '\\'
    ): array {
        $flag_escape_on = false;
        $flag_modifier_on = false;
        $length = mb_strlen($line);
        $modifiers_command = "";
        $modifiers = [];

        for ($i = 0; $i < $length; $i++) {
            // エスケープ中か？
            if ($flag_escape_on) {
                // 修飾コマンド中なら修飾コマンド文字列中に含める？
                if ($flag_modifier_on) {
                    $modifiers_command .= $line[$i];
                }
                $flag_escape_on = false;
                continue;
            }
            // 文中のエスケープ文字か？
            if ($line[$i] == $escape) {
                // 修飾コマンド中なら修飾コマンド文字列中に含める？
                if ($flag_modifier_on) {
                    $modifiers_command .= $line[$i];
                }
                $flag_escape_on = true;
                continue;
            }

            // 修飾コマンド開始か？
            if (!$flag_modifier_on && $line[$i] == $modifiers_key[0]) {
                $modifiers_command = "";
                $flag_modifier_on = true;
                $modifiers_command .= $line[$i];
                continue;
            }
            // 修飾コマンド終了か？
            if ($flag_modifier_on && $line[$i] == $modifiers_key[1]) {
                $modifiers_command .= $line[$i];
                $modifiers[] = $modifiers_command;
                $flag_modifier_on = false;
                continue;
            }
            // 修飾コマンド中か？
            if ($flag_modifier_on) {
                $modifiers_command .= $line[$i];
            }
        }
        return $modifiers;
    }
}
