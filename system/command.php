<?php
/**
 * command.php
 *
 * @package Hasarius
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
     * @throws Exception テンプレートファイルが存在しない場合に例外を発生
     */
    public function trancelate(Vessel &$parsed): void
    {
        // コマンドの場合はID生成(id_ + "行番号")
        $parsed->setId('id_' . $parsed->getLineNumber());
        // 自動インデント設定
        $parsed->setAutoIndent($this->isAutoIndent());

        // 一応保険としてコマンドの確認を実施
        if (!empty($parsed->getCommand()) && $this->getCommandName() == $parsed->getCommand()) {
            // ブロックタイプ設定
            $parsed->setBlockType($this->getBlockType());
            $parserParamaters = $parsed->getParamaters();
            // 開始タグ
            $parameters = "";
            foreach ($parserParamaters as $key => $value) {
                $parameters .= " " . $key . '="' . $value . '"';
            }
            $tagOpen = '<' . $this->getTagOpen() . $parameters;
            $parsed->setTagOpen($tagOpen . '>');
            // 終了タグ（スタック用）
            $parsed->setTagClose($this->getTagClose());
            // scriptがあるならばそのデータを定義
            $filename = $this->getScriptFile();
            if (!empty($filename)) {
                if (!file_existx($filename)) {
                    // ファイルが存在しない場合は例外発生
                    throw new \Exception("Script template file is not exists !! (" . $filename . ")");
                }
                $parsed->setScript($this->makeScriptString($parsed->getId(), HASARIUS_COMMANDS_DIR, $filename));
            }
            // 独自CSSがあるならばそのデータを定義
            $filename = $this->getCssFile();
            if (!empty($filename)) {
                if (!file_exists($filename)) {
                    // ファイルが存在しない場合は例外発生
                    throw new \Exception("CSS template file is not exists !! (" . $filename . ")");
                }
                $parsed->setCss($this->makeScriptString($parsed->getId(), HASARIUS_COMMANDS_DIR, $filename));
            }
        }
    }

    /**
     * 次行以降でコマンドに付随した処理を継続して設定したい場合に継承先クラスで定義する
     * 処理の結果については $line->setBatch() で設定する
     * @param Vessel $line [description]
     * @throws Exception なんらかのエラー時に例外を発生させる
     */
    public function execSubCommand(Vessel &$line): void
    {
        throw new \Exception("Irregular Call !!  (Command Name = " . $line->getCommand() . ")");
    }
}
