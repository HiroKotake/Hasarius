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

    // コマンド種別
    const COMMAND_TYPE_SYSTEM =    1;
    const COMMAND_TYPE_HTML   =   10;
    const COMMAND_TYPE_CSS    =  100;
    const COMMAND_TYPE_SCRIPT = 1000;

    // ドキュメントタイプ種別
    const DOCUMENT_TYPE_HTML4_LOOSE  = 1;
    const DOCUMENT_TYPE_HTML4_STRICT = 2;
    const DOCUMENT_TYPE_HTML4_FRAME  = 3;
    const DOCUMENT_TYPE_XHTML1       = 10;
    const DOCUMENT_TYPE_HTML5        = 20;

    // コマンド挙動確定用変数：以下の変数は継承先コンストラクタ内で設定する必要がある
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
     *                 COMMAND_TYPE_SYSTEM(1)       ... システム用コマンド
     *                 COMMAND_TYPE_HTML(10)        ... HTML用コマンド
     *                 COMMAND_TYPE_CSS(100)        ... CSS用コマンド
     *                 COMMAND_TYPE_SCRIPT(1000)    ... Script用コマンド
     */
    protected $commandPerpose        = null;
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
    protected $possibleDocumentTypes = null;
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
     * @param int $blockType タグの範囲。BLOCK_TYPE_ONLINE:HTMLファイル出力は閉じタグを含む、BLOCK_TYPE_SPARATE:HTMLファイル出力は閉じタグを含まない。
     */
    protected function setBlockType(int $blockType): void
    {
        $this->blockType = $blockType;
    }
    /**
     * タグの範囲設定を取得
     * @return int [description]
     */
    public function getBlockType(): int
    {
        return $this->blockType;
    }

    /**
     * コマンドの機能範囲を設定
     * @param array $commandPerposeList 機能リスト
     */
    protected function setCommandPerpose(array $commandPerposeList): void
    {
        $this->commandPerpose = $commandPerposeList;
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
        $this->possibleDocumentTypes = $htmlTypes;
    }
    /**
     * 利用可能なドキュメントタイプを取得
     * @return array ドキュメントタイプリスト
     */
    public function getPossibleDocumentTypes(): array
    {
        return $this->possibleDocumentTypes;
    }
    /**
     * 利用可能なドキュメントタイプか判定
     * @param  string $docType ドキュメントタイプ文字列
     * @return bool            利用可能ならば真を、利用不可能ならば偽を返す
     */
    public function isAbleDocumentType(string $docType): bool
    {
        return in_array($docType, $this->possibleDocumentTypes);
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

    // パラメータチェック
    private function subVerifyParamater(string $paramName, $paramValue)
    {
    }

    public function varifiyParamaters(array $paramaters): boolean
    {
    }
    // 生成後の内容掃き出し
}
