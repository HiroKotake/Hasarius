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

/**
 * コマンド基底クラス
 *
 * @package hasarius
 * @category system
 * @author Takahiro Kotake
 */
class Command
{
    // 影響範囲
    const BLOCK_TYPE_ONE_LINE = 1;
    const BLOCK_TYPE_SPARATE  = 2;
    const BLOCK_TYPE_BOTH     = 3;

    private $listBlockTypeByString = [
        "ONE_LINE" => 1,
        "SPARATE"  => 2,
        "BOTH"     => 3,
    ];

    private $listBlockTypeByNumber = [
        1 => "ONE_LINE",
        2 => "SPARATE",
        3 => "BOTH",
    ];

    // コマンド種別
    const COMMAND_PERPOSE_SYSTEM = 1;
    const COMMAND_PERPOSE_HTML   = 2;
    const COMMAND_PERPOSE_CSS    = 4;
    const COMMAND_PERPOSE_SCRIPT = 8;

    private $listCommandPerposeByString = [
        "SYSTEM" => 1,
        "HTML"   => 2,
        "CSS"    => 4,
        "SCRIPT" => 8,
    ];

    private $listCommandPerposeByNumber = [
        1 => "SYSTEM",
        2 => "HTML",
        4 => "CSS",
        8 => "SCRIPT",
    ];

    // ドキュメントタイプ種別
    const DOCUMENT_TYPE_HTML4_LOOSE   = 1;
    const DOCUMENT_TYPE_HTML4_STRICT  = 2;
    const DOCUMENT_TYPE_HTML4_FRAME   = 3;
    const DOCUMENT_TYPE_XHTML1_LOOSE  = 10;
    const DOCUMENT_TYPE_XHTML1_STRICT = 11;
    const DOCUMENT_TYPE_XHTML1_FRAME  = 12;
    const DOCUMENT_TYPE_XHTML1_1      = 13;
    const DOCUMENT_TYPE_HTML5         = 20;
    const DOCUMENT_TYPE_HTML5_1       = 21;

    private $listDocumentTypeByString = [
        "HTML4_LOOSE"   =>  1,
        "HTML4_STRICT"  =>  2,
        "HTML4_FRAME"   =>  3,
        "XHTML1_LOOSE"  => 10,
        "XHTML1_STRICT" => 11,
        "XHTML1_FRAME"  => 12,
        "XHTML1_1"      => 13,
        "HTML5"         => 20,
        "HTML5_1"       => 21,
    ];

    private $listDocumentTypeByNumber = [
         1 => "HTML4_LOOSE",
         2 => "HTML4_STRICT",
         3 => "HTML4_FRAME",
        10 => "XHTML1_LOOSE",
        11 => "XHTML1_STRICT",
        12 => "XHTML1_FRAME",
        13 => "XHTML1_1",
        20 => "HTML5",
        21 => "HTML5_1",
    ];

    const PARAMETERS_TYPE_TAG = 1;  // パラメータタイプ： HTML TAG
    const PARAMETERS_TYPE_CSS = 2;  // パラメータタイプ： CSS

    // コマンド挙動確定用変数：以下の変数は継承先コンストラクタ内で設定する必要がある
    /**
     * コマンド名
     * @var string コマンド名
     */
    protected $commandName          = null;

    /**
     * 開始タグ文字列
     * @var string|null 開始用のHTMLタグ文字列
     */
    protected $tagOpen               = null;
    /**
     * 終了タグ文字列
     * @var string|null 終了用のHTMLタグ文字列
     */
    protected $tagClose              = null;
    /**
     * 範囲タイプ
     * @var int 範囲指定 BLOCK_TYPE_ONLINE(1)    ... インラインタグ
     *                  BLOCK_TYPE_SPARATE(2)   ... 複数のタグを含んだ範囲で閉じる
     *                  BLOCK_TYPE_BOTH(3)      ... インライン、範囲の両方
     */
    protected $blockType             = self::BLOCK_TYPE_ONE_LINE;
    /**
     * コマンドの利用種別
     * @var array|null コマンドの適用先(sytem, html, css, script)
     *                 配列に含まれる可能性のある要素
     *                 COMMAND_PERPOSE_SYSTEM(1)       ... システム用コマンド
     *                 COMMAND_PERPOSE_HTML(10)        ... HTML用コマンド
     *                 COMMAND_PERPOSE_CSS(100)        ... CSS用コマンド
     *                 COMMAND_PERPOSE_SCRIPT(1000)    ... Script用コマンド
     */
    protected $commandPerpose        = [];
    /**
     * コマンド呼び出し用エイリアス
     * @var string|null エイリアス文字列
     */
    protected $commandAlias          = null;
    /**
     * 使用可能ドキュメントタイプ
     * @var array|null 使用可能ドキュメントタイプのリスト
     *                 配列に含まれる可能性のある要素
     *                 DOCUMENT_TYPE_HTML4_LOOSE(1)
     *                 DOCUMENT_TYPE_HTML4_STRICT(2)
     *                 DOCUMENT_TYPE_HTML4_FRAME(3)
     *                 DOCUMENT_TYPE_XHTML1(10)
     *                 DOCUMENT_TYPE_HTML5(20)
     */
    protected $possibleDocumentTypes = [];
    /**
     * 現在のドキュメントタイプ
     * @var int
     */
    protected $currentDocumentType = null;
    /**
     * 使用可能なタグの属性リスト
     * @var array|null 使用可能なタグの属性のリスト
     */
    protected $possibleTagAttributes = null;
    /**
     * 使用可能なCSSの属性リスト
     * @var array|null 使用可能なCSSの属性のリスト
     */
    protected $possibleCssAttributes = null;

    /**
     * コンストラクタ
     * @param string $jsonFile コマンド設定用JSONファイル
     * @throws Exception 何らかのエラーが発生した場合は例外を発生させる
     */
    public function __construct($jsonFile)
    {
        $this->currentDocumentType = CURRENT_DOCUMENT_DATA;
        try {
            $this->loadSettingJsonFile($jsonFile);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * コマンド名を設定
     * @param string $commandName コマンド名
     */
    protected function setCommandName(string $commandName): void
    {
        $this->commandName = $commandName;
    }
    /**
     * コマンド名を取得
     * @return string コマンド名
     */
    protected function getCommandName(): string
    {
        return $this->commandName;
    }
    /**
     * 開始タグを設定
     * @param string $tag 開始タグの文字列
     */
    protected function setTagOpen(string $tag): void
    {
        $this->tagOpen = $tag;
    }
    /**
     * 開始タグを取得
     * @return string 開始タグ文字列
     */
    public function getTagOpen(): string
    {
        return $this->tagOpen;
    }

    /**
     * 終了タグを設定
     * @param string $tag 終了タグの文字列
     */
    protected function setTagClose(string $tag): void
    {
        $this->tagClose = preg_match('/^<\/[a-zA-Z0-9]+>$/', $tag) == 1 ? $tag : '</' . $tag . '>';
    }
    /**
     * 終了タグを取得
     * @return string 終了タグの文字列
     */
    public function getTagClose(): string
    {
        return $this->tagClose;
    }

    /**
     * タグの範囲設定を設定
     * @param string $blockType タグの範囲。BLOCK_TYPE_ONLINE:HTMLファイル出力は閉じタグを含む、BLOCK_TYPE_SPARATE:HTMLファイル出力は閉じタグを含まない。
     */
    protected function setBlockType(string $blockType): void
    {
        $this->blockType = $this->listBlockTypeByString[$blockType];
    }
    /**
     * タグの範囲設定を取得
     * @return int タグの範囲設定
     */
    public function getBlockType(): int
    {
        return $this->blockType;
    }
    /**
     * タグの範囲設定を文字列として取得
     * @return string タグの範囲設定文字列
     */
    public function getBlockTypeWithString(): string
    {
        return $this->listBlockTypeByNumber[$this->blockType];
    }

    /**
     * コマンドの機能範囲を設定
     * @param array $commandPerposeList 機能リスト
     */
    protected function setCommandPerpose(array $commandPerposeList): void
    {
        foreach ($commandPerposeList as $commandPerpose) {
            $this->commandPerpose[] = $this->listCommandPerposeByString[$commandPerpose];
        }
    }
    /**
     * コマンドの機能範囲を文字列を含んだ配列にて取得
     * @return array コマンドの機能範囲を文字列を含んだ配列
     */
    public function getCommandPerposeWithString(): array
    {
        $list = [];
        foreach ($this->commandPerpose as $commandPerpose) {
            $list[] = $this->listCommandPerposeByNumber[$commandPerpose];
        }
        return $list;
    }

    /**
     * コマンドエイリアスを設定
     * @param string $alias コマンドエイリアス
     */
    protected function setCommandAlias(string $alias): void
    {
        $this->commandAlias = $alias;
    }
    /**
     * コマンドエイリアスを取得
     * @return string コマンドエイリアス
     */
    public function getCommandAlias(): string
    {
        return $this->commandAlias;
    }

    /**
     * 利用可能なHTMLドキュメントタイプを設定
     * @param array $htmlTypes ドキュメントタイプリスト
     */
    protected function setPossibleDocumentTypes(array $htmlTypes): void
    {
        foreach ($htmlTypes as $type) {
            $this->possibleDocumentTypes[] = $this->listDocumentTypeByString[$type];
        }
    }
    /**
     * 利用可能なドキュメントタイプ(数値)を取得
     * @return array ドキュメントタイプリスト
     */
    public function getPossibleDocumentTypes(): array
    {
        return $this->possibleDocumentTypes;
    }
    /**
     * 利用可能なドキュメントタイプ(文字列)を取得
     * @return array ドキュメントタイプリスト
     */
    public function getPossibleDocumentTypesWithString(): array
    {
        $result = [];
        foreach ($this->possibleDocumentTypes as $type) {
            $result[] = $this->listDocumentTypeByNumber[$type];
        }
        return $result;
    }

    /**
     * 利用可能なドキュメントタイプか判定
     * @param  int  $docType ドキュメントタイプ
     * @return bool          利用可能ならば真を、利用不可能ならば偽を返す
     */
    public function isAbleDocumentType(int $docType): bool
    {
        return in_array($docType, $this->possibleDocumentTypes);
    }
    /**
     * 利用可能なドキュメントタイプか判定
     * @param  string $docType ドキュメントタイプ文字列
     * @return bool            利用可能ならば真を、利用不可能ならば偽を返す
     */
    public function isAbleDocumentTypeByString(string $docType): bool
    {
        return in_array($this->listDocumentTypeByNumber[$docType], $this->possibleDocumentTypes);
    }

    /**
     * 利用可能なタグの属性を設定
     * @param array $attributes 利用可能なタグのリスト
     */
    protected function setPossibleTagAttributes(array $attributes): void
    {
        $this->possibleTagAttributes = $attributes;
    }
    /**
     * 利用可能なタグのリストを取得
     * @return array 利用可能なタグのリスト
     */
    public function getPossibleTagAttributes(): array
    {
        return $this->possibleTagAttributes;
    }
    /**
     * 利用可能なタグの属性か判定
     * @param  string $attribute 属性名
     * @return bool              利用可能なタグの属性ならば真を、利用不可能なタグの属性ならば偽を返す
     */
    public function isAbleTagAttribute(string $attribute): bool
    {
        return array_key_exists($attribute);
    }

    /**
     * 利用可能なCSSの属性のリストを取得
     * @param array $attributes 利用可能なCSSの属性のリスト
     */
    protected function setPossibleCssAttributes(array $attributes): void
    {
        $this->possibleCssAttributes = $attributes;
    }
    /**
     * 利用可能なCSSの属性のリストを取得
     * @return array 利用可能なCSSの属性のリスト
     */
    public function getPossibleCssAttributes(): array
    {
        return $this->possibleCssAttributes;
    }

    /**
     * コマンド設定用JSONファイルを読み込み、設定値を格納
     * @param string $filename コマンド設定用JSONファイル
     * @throws Exception ファイル存在しない、ファイルが開けない場合に例外を発生
     */
    public function loadSettingJsonFile(string $filename): void
    {
        try {
            if (!file_exists($filename)) {
                throw new \Exception('File Not Found !! - ' . $filename);
            }
            $json = file_get_contents($filename);
            $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
            $settings = json_decode($json, true);
            // 以下に設定を読み込む処理を記述
            $this->setCommandName($settings["CommandName"]);
            $this->setTagOpen($settings["TagOpen"]);
            $this->setTagClose($settings["TagClose"]);
            $this->setBlockType($settings["BlockType"]);
            $this->setCommandPerpose($settings["CommandPerposes"]);
            $this->setCommandAlias($settings["CommandAlias"]);
            $this->setPossibleDocumentTypes($settings["DocumentType"]);
            $this->setPossibleTagAttributes($settings["TagAttributes"]);
            $this->setPossibleCssAttributes($settings["CssAttributes"]);
            // json変数を解放
            unset($json);
        } catch (Exception $e) {
            throw $e;
        }
    }

    // パラメータチェック
    private function subVerifyParamater(int $target, string $paramName, $paramValue): string
    {
        $result = "";
        $lightValue = $this->possibleTagAttributes[$this->currentDocumentType][$paramName];
        if ($target == self::PARAMETERS_TYPE_CSS) {
            $lightValue = $this->possibleCssAttributes[$paramName];
        }
        // ToDo 属性値が正しい値か確認
        return $result;
    }

    /**
     * Tagパラメータが正しく設定されているか確認する
     * @param  array $paramaters 属性名と属性値のペアを含んだ連想配列
     * @return array             何らかのエラーが発生している場合は、エラー文字列を含んだ配列を返す。問題がない場合は空配列を返す
     */
    public function varifiyTagParamaters(array $paramaters): array
    {
        $errorList = [];
        foreach ($paramaters as $key => $value) {
            if ($key == 'Style') {
                // CSS
                $result = $this->varifiyCssParamaters($value);
                if (!empty($result)) {
                    array_merge($errorList, $result);
                }
            } else {
                // その他
                $result = $this->subVerifyParamater(self::PARAMETERS_TYPE_TAG, $key, $value);
                if (!empty($result)) {
                    $errorList[] = $result;
                }
            }
        }
        return $errorList;
    }

    /**
     * CSSパラメータが正しく設定されているか確認する
     * @param  string $paramaters 属性名と属性値のペアを含んだ連想配列
     * @return array             何らかのエラーが発生している場合は、エラー文字列を含んだ配列を返す。問題がない場合は空配列を返す
     */
    public function varifiyCssParamaters(string $paramaters): array
    {
        $errorList = [];
        $paramsWork = explode(';', $paramaters);
        $params = [];
        foreach ($paramsWork as $value) {
            list($key, $val) = explode(':', $value);
            $params[$key] = $val;
        }
        foreach ($params as $key => $value) {
            $result = $this->subVerifyParamater(self::PARAMETERS_TYPE_CSS, $key, $value);
            if (!empty($result)) {
                $errorList[] = $result;
            }
        }
        return $errorList;
    }

    /**
     * 設定ファイルを読み込み、文字列を生成
     * @param  string $id       ID
     * @param  string $filename 設定ファイル
     * @return string           加工した文字列
     */
    private function makeExtentionString(string $id, string $filename): string
    {
        $text = '';
        try {
            $hFile = fopen($filename, 'r');
            while (($line = fgets($hFile)) !== false) {
                $text .= str_replace("@ID@", $id, rtrim($line)) . PHP_EOL;    // ToDo: 文字エンコードの変換が必要
            }
            fclose($hFile);
        } catch (\Exception $e) {
            echo '[ERROR] FILE I/O Error !! (' . $filename . ')';
        }
    }
    /**
     * スクリプト文字列を生成
     * @param  string $id       ID
     * @param  string $filename 元となるスクリプトを含んだファイル
     * @return string           加工したスクリプト文字列
     */
    public function makeScriptString(string $id, string $filename = null): string
    {
        $script = "";
        if (empty($filename) || !file_exists($filename)) {
            // ファイルが存在しない場合はデフォルト値を生成
            $filename = HASARIUS_COMMANDS_DIR
                      . DIRECTORY_SEPARATOR
                      . $this->getCommandName()
                      . DIRECTORY_SEPARATOR
                      . $this->getCommandName() . '.jsp';
        }
        $script = $this->makeExtentionString($id, $filename);
        return $script;
    }

    /**
     * 独自CSSを生成
     * @param  string $id       ID
     * @param  string $filename 元となるCSSを含んだファイル
     * @return string           加工したCSS文字列
     */
    public function makeCssString(string $id, string $filename = null): string
    {
        $css = "";
        if (empty($filename) || !file_exists($filename)) {
            // ファイルが存在しない場合はデフォルト値を生成
            $filename = HASARIUS_DECORATION_DIR
                      . DIRECTORY_SEPARATOR
                      . $this->getCommandName()
                      . DIRECTORY_SEPARATOR
                      . $this->getCommandName() . '.css';
        }
        $css = $this->makeExtentionString($id, $filename);
        return $css;
    }

    /**
     * コマンドに対応した文字列変換を実施
     * - インラインコマンドについてはsystem/Generateで別途に操作を行う
     * @param  array $parsed Parser経由での行解析結果配列
     *                          'command' => コマンド名（文字列ののみの場合は空)
     *                          'paramaters' => コマンドの属性と属性値
     *                          'modifiers' => インラインコマンドの情報配列
     *                          'text' => 表示するテキストがある場合はその文字列、無い場合は空文字
     *                          'comment' => コメント文字列
     *                          'lineNumber' => 行番号
     * @return array         操作後の結果を含んだデータの配列
     *                          (引数の配列に以下の要素を追加する)
     *                          'id' => コマンドであるならば id を示す文字列を、コマンドでない場合はnull
     *                          'tagOpen' => コマンドであるならば開始タグを示す文字列を、コマンドでない場合はnull
     *                          'tagClose' => コマンドであるならば終了タグを示す文字列を、コマンドでない場合はnull
     *                          'script' => コマンドに必要なスクリプトがある場合はスクリプトを、コマンドで出ない場合はnull
     *                          'css' => コマンドに独自のCSSがある場合はCSSを、コマンドで出ない場合はnull
     */
    public function trancelate(array $parsed): array
    {
        // 一応保険としてコマンドの確認を実施
        if (!empty($parsed['command']) && $this->getCommandName() == $parsed['command']) {
            // コマンドの場合はID生成(id_ + "行番号")
            $parsed['id'] = 'id_' . $parsed['lineNumber'];
            // ToDo: コマンドの属性と属性値の正当性確認
            $params = $this->varifiyTagParamaters($parsed['paramaters']);
            // 開始タグ
            $tagOpen = '<' . $this->getTagOpen();
            foreach ($params as $key => $value) {
                $tagOpen . ' ' . $key . '=""' . $value . '"';
            }
            $parsed['tagOpen'] = $tagOpen . '>';
            // 終了タグ（スタック用）
            $parsed['tagClose'] = $this->getTagClose();
            // scriptがあるならばそのデータを定義
            $filename = null;
            if (array_key_exists('ScriptFile', $parsed['paramaters'])) {
                $filename = $parsed['paramaters']['ScriptFile'];
            }
            $parsed['script'] = $this->makeScriptString($id, $filename);
            // 独自CSSがあるならばそのデータを定義
            $filename = null;
            if (array_key_exists('CssFile', $parsed['paramaters'])) {
                $filename = $parsed['paramaters']['CssFile'];
            }
            $parsed['css'] = $this->makeScriptString($id, $filename);
        }
        return $parsed;
    }
}
