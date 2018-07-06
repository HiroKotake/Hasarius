<?php
/**
 * genarate.php
 *
 * @package hasarius
 * @category system
 * @author Takahiro Kotake
 * @license Teleios Development
 */

namespace Hasarius\system;

use Hasarius\utils as utils;

/**
 * HTML 生成クラス
 *
 * @package hasarius
 * @category system
 * @author Takahiro Kotake
 */
class Genarate
{
    /**
     * コマンド保持マップ
     * @var array
     */
    private $commands = [];
    /**
     * コマンドエイリアス保持マップ
     * @var array
     */
    private $commandAlias = [];
    /**
     * 修飾コマンド保持マップ
     * @var array
     */
    private $decorations = [];
    /**
     * 修飾コマンドエイリアス保持マップ
     * @var array
     */
    private $decorationsAlias = [];
    /**
     * クローズスタック
     * @var string
     */
    private $closerStack = [];

    /**
     * クローズスタックからアイテムを取得
     * @return string アイテム
     */
    private function popc():string
    {
        if (empty($this->closerStack)) {
            return null;
        }
        return array_pop($this->closerStack);
    }

    /**
     * クローズスタックにアイテムを積む
     * @param string $item アイテム
     */
    private function pushc(string $item): void
    {
        $this->closerStack[] = $item;
    }

    public function __construct()
    {
        $this->initialize();
    }

    /**
     * 初期設定実施
     */
    private function initialize() : void
    {
        // directory解析
        $dirMap = explode(DIRECTORY_SEPARATOR, __DIR__);
        array_pop($dirMap);
        $baseDir = implode(DIRECTORY_SEPARATOR, $dirMap);
        define('HASARIUS_BASE_DIR', $baseDir);
        define('HASARIUS_SYSTEM_DIR', HASARIUS_BASE_DIR . DIRECTORY_SEPARATOR . 'system');
        define('HASARIUS_UTILS_DIR', HASARIUS_BASE_DIR . DIRECTORY_SEPARATOR . 'utils');
        define('HASARIUS_COMMANDS_DIR', HASARIUS_BASE_DIR . DIRECTORY_SEPARATOR . 'commands');
        define('HASARIUS_DECORATION_DIR', HASARIUS_BASE_DIR . DIRECTORY_SEPARATOR . 'decoration');

        // system 読み込み
        require_one(HASARIUS_SYSTEM_DIR . DIRECTORY_SEPARATOR . 'command.php');
        require_one(HASARIUS_SYSTEM_DIR . DIRECTORY_SEPARATOR . 'decoration.php');
        require_one(HASARIUS_SYSTEM_DIR . DIRECTORY_SEPARATOR . 'vessel.php');

        // Utility 読み込み
        require_once(HASARIUS_UTILS_DIR . DIRECTORY_SEPARATOR . 'parser.php');
        require_once(HASARIUS_UTILS_DIR . DIRECTORY_SEPARATOR . 'validater.php');

        // commands 読み込み
        $commandDir = dir(HASARIUS_COMMANDS_DIR);
        while (false !== ($file = $commandDir->read())) {
            if ($file != '.' || $file != '..') {
                // phpファイル読み込み
                $commandFileName = HASARIUS_COMMANDS_DIR
                                    . DIRECTORY_SEPARATOR
                                    . $file
                                    . DIRECTORY_SEPARATOR
                                    . $file . '.php';
                require_once($commandFileName);
                // クラス生成
                $this->commands[$file] = new $file();
                $this->commandAlias[$this->commands[$file]->ALIAS] = $file;
            }
        }
        $commandDir->close();

        // dcecoration 読み込み
        $decorationDir = dir(HASARIUS_DECORATION_DIR);
        while (false !== ($file = $decorationDir->read())) {
            if ($file != '.' || $file != '..') {
                // phpファイル読み込み
                require_once(HASARIUS_DECORATION_DIR . DIRECTORY_SEPARATOR . $file . '.php');
                // クラス生成
                list($className, $exp) = explode('.', $file);
                $this->decorations[$className] = new $className();
                $this->decorationsAlias[$this->decorations[$className]->ALIAS] = $className;
            }
        }
        $decorationDir->close();
    }

    /**
     * HTMLファイル生成
     * @param  string $source [description]
     * @return bool           [description]
     */
    public function make(string $source): bool
    {
        // 設定ファイル読み込み
        $sourcePath = explode(DIRECTORY_SEPARATOR, $source);
        array_pop($sourcePath);
        $makeConfigFile = implode(DIRECTORY_SEPARATOR, $sourcePath) . DIRECTORY_SEPARATOR . 'make.cfg';
        if (!file_exists($makeConfigFile)) {
            $makeConfigFile = 'HASARIUS_BASE_DIR' . DIRECTORY_SEPARATOR . 'make.cfg';
        }
        require_once($makeConfigFile);

        // 解析
        try {
            self::analyze($source);
        } catch (Exception $e) {
            var_dump($e);
            return false;
        }

        return true;
    }

    public function analyze(string $source, $lineNumber = 0) : int
    {
        try {
            // 解析
            if (!file_exists($source)) {
                throw new \Exception("[ERROR] FILE NOT EXISTS !!", 1);
            }
            //  - ファイルオープン
            $hFile = fopen($source, 'r');
            //  -- 行読み込み
            while (($line = fgets($hFile)) !== false) {
                $lineNumber++;    // 行インデックス更新
                //  --- 解析
                $lineParameters = utils\Parser::analyzeLine($line);
                if ($lineParameters['command'] == 'include') {
                    // --- 外部ソース読み込み
                    $lineNumber = self::analyze($lineParameters['text'], $lineNumber);
                } else {
                    //  --- 出力
                    //  ---- 修飾エイリアス確認
                    //  ---- 修飾エイリアスになければ実態を確認
                    //  ---- テキスト置換
                    //  ---- コマンドエイリアス確認
                    //  ---- コマンドエイリアスになければ実態を確認
                    //  ---- HTML生成
                }
            }
            //  - ファイルクローズ
            fclose($hFile);
        } catch (Exception $e) {
            echo 'Error at line nunber (' . $lineNumber . '): ' . $line . PHP_EOL;
            throw $e;
        }
        return $lineNumber;
    }
}
