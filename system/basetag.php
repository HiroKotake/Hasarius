<?php
/**
 * basetag.php
 *
 * @package Hasarius
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
class BaseTag
{
    // 影響範囲 (command, decorationの各クラスはどれか一つしかブロックタイプを取ることしかできない)
    const BLOCK_TYPE_NONE    = 0;    // テキストオンリーの場合のみ使用
    const BLOCK_TYPE_INLINE  = 1;    // 一行のテキストの中の任意の文字列でで終了するコマンド
    const BLOCK_TYPE_BLOCK   = 2;    // 複数行にまたがって記述され間に別のコマンドがある場合に指定するコマンド
    const BLOCK_TYPE_BOTH    = 3;    // ブロックもしくはインラインの両方で使用できるコマンド
    const BLOCK_TYPE_ONELINE = 4;    // 一行で完了するブロックコマンド
    const BLOCK_TYPE_BATCH   = 5;   // 間に何も挟まず、外部ソース等を読み込んだりして動作するコマンド

    private $listBlockTypeByString = [
        "NONE"   => 0,
        "INLINE" => 1,
        "BLOCK"  => 2,
        "BOTH"   => 3,
        "ONELINE" => 4,
        "BATCH"  => 5,
    ];

    private $listBlockTypeByNumber = [
        0 => "NONE",
        1 => "INLINE",
        2 => "BLOCK",
        3 => "BOTH",
        4 => "ONELINE",
        5 => "BATCH",
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

    // スクリプトの配置先
    const SCRIPT_PLACE_TYPE_FILE       = "FILE";        // ファイル
    const SCRIPT_PLACE_TYPE_FILE_READY = "FILE_READY";  // ファイル
    const SCRIPT_PLACE_TYPE_HEAD       = "HEAD";        // ヘッダ部
    const SCRIPT_PLACE_TYPE_HEAD_READY = "HEAD_READY";  // ヘッダ部
    const SCRIPT_PLACE_TYPE_BODY       = "BODY";        // ボディ部

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
     * @var int 範囲指定 BLOCK_TYPE_INLINE(1)    ... インラインタグ
     *                  BLOCK_TYPE_BLOCK(2)     ... 複数のタグを含んだ範囲で閉じる
     *                  BLOCK_TYPE_BOTH(3)      ... インライン、範囲の両方
     */
    protected $blockType             = self::BLOCK_TYPE_INLINE;
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
     * 使用可能な一般タグの属性リスト
     * @var array|null 使用可能な一般タグの属性のリスト
     */
    protected $possibleGlobalAttributes = null;
    /**
     * 使用可能なタグのイベント属性リスト
     * @var array|null 使用可能なタグのイベント属性のリスト
     */
    protected $possibleEventAttributes = null;
    /**
     * 使用可能なタグの属性リスト
     * @var array|null 使用可能なタグの属性のリスト
     */
    protected $possibleTagAttributes = null;
    /**
     * 使用可能なCSSの属性リスト
     * @var array|null 使用可能なCSSの属性のリスト
     */
    protected $possibleCustomAttributes = null;
    /**
     * 自動インデントを使用する
     * @var int
     */
    protected $autoIndent = 1;
    /**
     * サブコマンドデータのリスト
     * @var array|null サブコマンドに関するデータのリスト
     */
    protected $subCommand = [];
    /**
     * スクリプトの配置先
     * @var string
     */
    protected $scriptSetType;

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
    public function getCommandName(): string
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
     * @param string $blockType タグの範囲。BLOCK_TYPE_INLINE:HTMLファイル出力は閉じタグを含む、BLOCK_TYPE_BLOCK:HTMLファイル出力は閉じタグを含まない。
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
     * 利用可能なタグの一般属性を設定
     * @param array $attributes 利用可能なタグの一般属性のリスト
     */
    protected function setPossibleGlobalAttributes(array $attributes): void
    {
        $this->possibleGlobalAttributes = $attributes;
    }
    /**
     * 利用可能なタグの一般属性を取得
     * @return array $attributes 利用可能なタグの一般属性のリスト
     */
    public function getPossibleGlobalAttributes(): array
    {
        return $this->possibleGlobalAttributes;
    }

    /**
     * 利用可能なタグのイベント属性を設定
     * @param array $attributes 利用可能なタグのイベント属性のリスト
     */
    protected function setPossibleEventAttributes(array $attributes): void
    {
        $this->possibleEventAttributes = $attributes;
    }
    /**
     * 利用可能なタグのイベント属性を取得
     * @return array $attributes 利用可能なタグのイベント属性のリスト
     */
    public function getPossibleEventAttributes(): array
    {
        return $this->possibleEventAttributes;
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
    protected function setPossibleCustomAttributes(array $attributes): void
    {
        $this->possibleCustomAttributes = $attributes;
    }
    /**
     * 利用可能なCSSの属性のリストを取得
     * @return array 利用可能なCSSの属性のリスト
     */
    public function getPossibleCustomAttributes(): array
    {
        return $this->possibleCustomAttributes;
    }

    /**
     * 自動インデントの使用を設定する
     * @param int $flag 0:使用しない、1:使用する
     */
    public function setAutoIndent(int $flag): void
    {
        $this->autoIndent = $flag;
    }
    /**
     * 自動インデントを使用するか
     * @return bool ture:使用する、false:使用しない
     */
    public function isAutoIndent(): bool
    {
        return ($this->autoIndent != 0);
    }

    /**
     * サブコマンドデータを設定
     * @param array $subCommand サブコマンドデータリスト
     */
    public function setSubCommand(array $subCommand): void
    {
        $this->subCommand = $subCommand;
    }
    /**
     * サブコマンドデータを取得
     * @return array サブコマンドデータリスト
     */
    public function getSubCommand(): array
    {
        return $this->subCommand;
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
            if (array_key_exists("CommandAlias", $settings) && strlen($settings["CommandAlias"]) > 0) {
                $this->setCommandAlias($settings["CommandAlias"]);
            }
            if (array_key_exists("GlobalAttributes", $settings)) {
                $this->setPossibleGlobalAttributes($settings["GlobalAttributes"]);
            }
            if (array_key_exists("EventAttributes", $settings)) {
                $this->setPossibleEventAttributes($settings["EventAttributes"]);
            }
            $this->setPossibleDocumentTypes($settings["DocumentType"]);
            $this->setPossibleTagAttributes($settings["TagAttributes"]);
            if (array_key_exists("CustomAttributes", $settings)) {
                $this->setPossibleCustomAttributes($settings["CustomAttributes"]);
            }
            if (array_key_exists("AutoIndent", $settings)) {
                $this->setAutoIndent($settings["AutoIndent"]);
            }
            if (array_key_exists("SubCommand", $settings)) {
                $this->setSubCommand($settings["SubCommand"]);
            }
            // json変数を解放
            unset($json);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // ToDo: チェック用のパターンが多くなるので考慮が必要 -> パターンをキーとした配列に載せて正規表現のパターンを取るようにすればいいかも
    // ToDo: 定義パターンから漏れたケースの場合は指定されている文字そのものを比較する
    private function subVerifyParamater(array $condition, $value): bool
    {
        // 正規表現用パターン生成
        $pattern = null;
        switch ($condition["type"]) {
            case "numeric":
                $pattern = "/^\d*\.?\d*$/u";
                $work = "";
                foreach ($condition["subtype"] as $val) {
                    $work .= $val . "|";
                }
                if (strlen($work) > 0) {
                    $pattern = "/\d*\.?\d*(" . rtrim($work, '|') . ")/u";
                }
                break;
            case "string":
                $pattern = "/.*/u";
                break;
        }
        // パターンチェック
        return preg_match($pattern, $value) > 0 ? true : false;
    }

    // パラメータチェック
    // - パラメータ名チェック
    // - パラメータ値チェック
    private function subVerifyTagParamater(array $info, $paramValue): bool
    {
        return $this->subVerifyParamater($info, $paramValue);
    }

    private function subVerifyCssParamater(string $paramName, $paramValue): bool
    {
        if (!array_key_exists($paramName, CSS_ATTRIBUTES)) {
            return false;
        }
        return $this->subVerifyParamater(CSS_ATTRIBUTES[$paramName], $paramValue);
    }

    private function attributeExist(string $name): array
    {
        if (array_key_exists($name, TAG_ATTRIBUTES)) {
            return TAG_ATTRIBUTE[$name];
        }
        if (array_key_exists($name, $this->possibleCustomAttributes)) {
            return $this->possibleCustomAttributes[$name];
        }
        foreach (TAG_ATTRIBUTES as $value) {
            if ($value['alias'] == $name) {
                return $value;
            }
        }
        // ToDo: カスタム属性では再度ドキュメントタイプをチェックする必要が発生したので改修が必要
        foreach ($this->possibleCustomAttributes as $value) {
            if ($value['alias'] == $name) {
                return $value;
            }
        }

        return [];
    }

    /**
     * CSSパラメータが正しく設定されているか確認する
     * @param  string $paramaters 属性名と属性値のペアを含んだ連想配列
     * @return array             何らかのエラーが発生している場合は、エラー文字列を含んだ配列を返す。問題がない場合は空配列を返す
     */
    public function varifiyCssParamaters(string $paramaters): array
    {
        $result = [];
        $paramsWork = explode(';', $paramaters);
        $params = [];
        foreach ($paramsWork as $value) {
            list($key, $val) = explode(':', $value);
            $params[$key] = $val;
        }
        foreach ($params as $key => $value) {
            if (!$this->subVerifyCssParamater($key, $value)) {
                $result[] = "CSS Attribute value is wrong !! [" . $key . " => " . $value . "]";
            }
        }
        return $result;
    }

    /**
     * Tagパラメータが正しく設定されているか確認する
     * @param  array $paramaters 属性名と属性値のペアを含んだ連想配列
     * @return array             何らかのエラーが発生している場合は、エラー文字列を含んだ配列を返す。問題がない場合は空配列を返す
     */
    public function varifiyTagParamaters(array $paramaters): array
    {
        $result = [];
        foreach ($paramaters as $key => $value) {
            // 属性名チェック
            $info = $this->attributeExist($key);
            if (empty($info)) {
                $result[] = "TAG Attribute is not exists !! [" . $key . "]";
                continue;
            }
            // 属性値チェック
            if ($key == 'Style') {
                // CSS Attribute
                $verified = $this->varifiyCssParamaters($value);
                if (!empty($verified)) {
                    $result = array_merge($result, $verified);
                }
            } else {
                // Tag Attribute
                if (!$this->subVerifyTagParamater($info, $value)) {
                    $result[] = "TAG Attribute value is wrong !! [" . $key . " => " . $value . "]";
                }
            }
        }
        return $result;
    }

    /**
     * スクリプト文字列を生成
     * @param  string $ident    ID
     * @param  string $baseDir  基準となるディレクトリ
     * @param  string $filename 元となるスクリプトを含んだファイル
     * @return string           加工したスクリプト文字列
     */
    public function makeScriptString(string $ident, string $baseDir, string $filename = null): string
    {
        $script = [];
        $currentPlaceType = SCRIPT_PLACE_TYPE_HEAD;
        if (empty($filename) || !file_exists($filename)) {
            // ファイルが存在しない場合はデフォルト値を生成
            $filename = $baseDir
                      . DIRECTORY_SEPARATOR
                      . $this->getCommandName()
                      . DIRECTORY_SEPARATOR
                      . $this->getCommandName() . '.jsp';
        }
        // 内容読み込み
        try {
            $hFile = fopen($filename, 'r');
            while (($line = fgets($hFile)) !== false) {
                $match = null;
                preg_match('/^\[(file|file_read|head|head_ready|body)\]$/iu', $line, $match);
                if (!empty($match)) {
                    $currentPlaceType = strtolower($match[1]);
                    continue;
                }
                $script[$currentPlaceType][] = str_replace("@ID@", $ident, rtrim($line)) . PHP_EOL;    // ToDo: 文字エンコードの変換が必要
            }
            fclose($hFile);
        } catch (\Exception $e) {
            echo '[ERROR] FILE I/O Error !! (' . $filename . ')';
        }
        return $script;
    }

    /**
     * 独自CSSを生成
     * @param  string $ident    ID
     * @param  string $baseDir  基準となるディレクトリ
     * @param  string $filename 元となるCSSを含んだファイル
     * @return string           加工したCSS文字列
     */
    public function makeCssString(string $ident, string $baseDir, string $filename = null): string
    {
        $css = "";
        if (empty($filename) || !file_exists($filename)) {
            // ファイルが存在しない場合はデフォルト値を生成
            $filename = $baseDir
                      . DIRECTORY_SEPARATOR
                      . $this->getCommandName()
                      . DIRECTORY_SEPARATOR
                      . $this->getCommandName() . '.css';
        }
        // 内容読み込み
        try {
            $hFile = fopen($filename, 'r');
            while (($line = fgets($hFile)) !== false) {
                $css .= str_replace("@ID@", $ident, rtrim($line)) . PHP_EOL;    // ToDo: 文字エンコードの変換が必要
            }
            fclose($hFile);
        } catch (\Exception $e) {
            echo '[ERROR] FILE I/O Error !! (' . $filename . ')';
        }
        return $css;
    }
}
