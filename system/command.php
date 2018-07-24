<?php
/**
 * command.php
 *
 * @package hasarius
 * @category system
 * @author Takahiro Kotake
 * @license Teleios Development
 */

namespace Hasarius\system;

use Hasarius\utils as Utils;
use Hasarius\system\Vessel;

/**
 * コマンド基底クラス
 *
 * @package hasarius
 * @category system
 * @author Takahiro Kotake
 */
class Command extends BaseTag
{
    /**
     * コマンドに対応した文字列変換を実施
     * - インラインコマンドについてはsystem/Generateで別途に操作を行う
     * @param  Vessel $parsed Parser経由での行解析結果配列
     *                          'command' => コマンド名（文字列ののみの場合は空)
     *                          'paramaters' => コマンドの属性と属性値
     *                          'modifiers' => インラインコマンドの情報配列
     *                          'text' => 表示するテキストがある場合はその文字列、無い場合は空文字
     *                          'comment' => コメント文字列
     *                          'lineNumber' => 行番号
     *                          (操作後以下の要素を追加する)
     *                          'id' => コマンドであるならば id を示す文字列を、コマンドでない場合はnull
     *                          'tagOpen' => コマンドであるならば開始タグを示す文字列を、コマンドでない場合はnull
     *                          'tagClose' => コマンドであるならば終了タグを示す文字列を、コマンドでない場合はnull
     *                          'script' => コマンドに必要なスクリプトがある場合はスクリプトを、コマンドで出ない場合はnull
     *                          'css' => コマンドに独自のCSSがある場合はCSSを、コマンドで出ない場合はnull
     */
    public function trancelate(Vessel &$parsed): void
    {
        // コマンドの場合はID生成(id_ + "行番号")
        $parsed->setId('id_' . $parsed['lineNumber']);

        // 一応保険としてコマンドの確認を実施
        if (!empty($parsed->getCommand()) && $this->getCommandName() == $parsed->getCommand()) {
            // ToDo: コマンドの属性と属性値の正当性確認
            $parserParamaters = $parsed->getParamaters();
            $params = $this->varifiytagparamaters($parserParamaters);
            // 開始タグ
            $tagOpen = '<' . $this->getTagOpen();
            foreach ($params as $key => $value) {
                $tagOpen . ' ' . $key . '="' . $value . '"';
            }
            $parsed->setTagOpen($tagOpen . '>');
            // 終了タグ（スタック用）
            $parsed->setTagClose($this->getTagClose());
            // scriptがあるならばそのデータを定義
            $filename = null;
            if (array_key_exists('ScriptFile', $parserParamaters)) {
                $filename = $parserParamaters['ScriptFile'];
            }
            $parsed->setScript($this->makeScriptString($parsed->getId(), HASARIUS_COMMANDS_DIR, $filename));
            // 独自CSSがあるならばそのデータを定義
            $filename = null;
            if (array_key_exists('CssFile', $parserParamaters)) {
                $filename = $parserParamaters['CssFile'];
            }
            $parsed->setCss($this->makeScriptString($parsed->getId(), HASARIUS_COMMANDS_DIR, $filename));
        }
    }
}
