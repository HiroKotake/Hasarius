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
 * コマンド基底クラス
 *
 * @package hasarius
 * @category system
 * @author Takahiro Kotake
 */
class Command
{
    const BLOCK_TYPE_ONE_LINE = 1;
    const BLOCK_TYPE_SPARATE  = 2;

    const COMMAND_TYPE_SYSTEM =    1;
    const COMMAND_TYPE_HTML   =   10;
    const COMMAND_TYPE_CSS    =  100;
    const COMMAND_TYPE_SCRIPT = 1000;

    // 処理用変数
    private $attribues  = [];
    private $csss       = [];
    private $scripts    = [];

    // コマンド挙動確定用変数：以下の変数は継承先コンストラクタ内で設定する必要がある
    private $tab_open        = null;
    private $tab_close       = null;
    private $block_type      = self::BLOCK_TYPE_ONE_LINE;
    private $command_perpose = null;

    // パラメータチェック
    private function subVerifyParamater(string $param_name, $param_value)
    {
    }

    public function varifiyParamaters(array $paramaters): boolean
    {
    }
    // 生成後の内容掃き出し
}
