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

/**
 * 修飾コマンド基底クラス
 *
 * @package hasarius
 * @category system
 * @author Takahiro Kotake
 */
class Decoration extends BaseTag
{
    /**
     * コマンドに対応した文字列変換を実施
     * @return array $params 解析結果を格納した連想配列
     *                       [
     *                          'id'      => id (仮ID:paramsに指定があればそちらを優先)
     *                          'command' => コマンド名文字列,
     *                          'params'  => 属性と属性値の文字列の配列
     *                          'text'    => 表示する文字列
     *                       ]
     * @return array         変換後の結果をを格納した連想配列
     *                       [
     *                          'id'      => id
     *                          'text'    => 変換後のHTML化した文字列
     *                          'script'  => スクリプトを格納した連想配列
     *                          'css'     => CSSを格納した連想配列
     *                       ]
     * @throws Exception テンプレートファイルが存在しない場合に例外を発生
     */
    public function trancelate(array $params): array
    {
        $result = [
            'id'     => null,
            'text'   => "",
            'script' => [],
            'css'    => "",
        ];
        // ID確定
        if (!array_key_exists('id', $params['params'])) {
            $params['params']['id'] = $params['id'];
        }
        $result['id'] = $params['params']['id'];
        // HTMLテキスト作成
        $result['text'] = $params['command'] . ' ';
        foreach ($params['params'] as $key => $value) {
            $result['text'] .= $key . '="' . $value . '" ';
        }
        $result['text'] .= $params['text'];
        // スクリプト対応
        $filename = null;
        if (array_key_exists('ScriptFile', $params['params'])) {
            $filename = $params['params']['ScriptFile'];
            // ファイルが存在しない場合は例外発生
            if (!file_existx($filename)) {
                throw new Exception("[ERROR] Script template file is not exists !! (" . $filename . ")");
            }
        }
        $result['script'] = $this->makeScriptString($result['id'], HASARIUS_DECORATION_DIR, $filename);
        // CSS対応
        $filename = null;
        if (array_key_exists('CssFile', $params['params'])) {
            $filename = $params['params']['CssFile'];
            // ファイルが存在しない場合は例外発生
            if (!file_existx($filename)) {
                throw new Exception("[ERROR] CSS template file is not exists !! (" . $filename . ")");
            }
        }
        $result['css'] = $this->makeCssString($result['id'], HASARIUS_DECORATION_DIR, $filename);

        return $result;
    }
}
