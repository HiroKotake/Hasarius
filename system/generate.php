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

use Hasarius\utils as Utils;
use Hasarius\command as Command;
use Hasarius\decorate as Decorate;

/**
 * HTML 生成クラス
 *
 * @package hasarius
 * @category system
 * @author Takahiro Kotake
 */
class Generate
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
     * コンテナ
     * @var array
     */
    private $vesselContainer = [];

    /**
     * Decoratetion用スクリプトコンテナ
     * @var array
     */
    private $decorateScript = [
        'fileReady' => [],
        'file'      => [],
        'headReady' => [],
        'head'      => [],
    ];

    /**
     * Decoratetion用スタイルシートコンテナ
     * @var [type]
     */
    private $decorateCss = [];

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
        define('HASARIUS_DECORATION_DIR', HASARIUS_BASE_DIR . DIRECTORY_SEPARATOR . 'decorations');

        // commands 読み込み
        $commandDir = dir(HASARIUS_COMMANDS_DIR);
        while (false !== ($file = $commandDir->read())) {
            if (($file != '.' || $file != '..') && is_dir($file)) {
                // クラス生成
                $className = 'Command\Command' . ucfirst($file);
                $this->commands[$file] = new $className();
                $this->commandAlias[$this->commands[$file]->getCommandAlias()] = $file;
            }
        }
        $commandDir->close();

        // dcecoration 読み込み
        $decorationDir = dir(HASARIUS_DECORATION_DIR);
        while (false !== ($file = $decorationDir->read())) {
            if (($file != '.' || $file != '..') && is_dir($file)) {
                // クラス生成
                $className = 'Decorate\Decorate' . ucfirst($file);
                $this->decorations[$file] = new $className();
                $this->decorationsAlias[$this->decorations[$file]->getDecorationAlias()] = $file;
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
        require_once($makeConfigFile);  // ToDo: jsonファイルに変更したい

        // HTML生成
        try {
            // 解析
            $this->analyze($source);
            // 構築
            $this->transform();
            // ファイル出力
            $this->genarate();
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }

        return true;
    }

    /**
     * 指定されたHasariusテキストファイルを解析
     * （再帰呼び出しあり）
     *
     * @param  string  $source     Hasariusテキストファイル
     * @param  integer $lineNumber 開始行番号
     * @return int                 最終行番号
     */
    public function analyze(string $source, $lineNumber = 0) : int
    {
        // STEP 1 : READ LINE
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
                $lineParameters = Utils\Parser::analyzeLine($line);
                if ($lineParameters->getCommand() == 'include') {
                    // --- 外部ソース読み込み
                    $lineNumber = self::analyze($lineParameters->getText(), $lineNumber);
                } else {
                    $command = $lineParameters->getCommand();
                    //  ---- コマンドエイリアス確認
                    if (in_array($command, $this->commandAlias)) {
                        $command = $this->commandAlias[$command];
                    }
                    //  ---- コマンドエイリアスになければ実態を確認
                    if (!in_array($command, $this->commands)) {
                        throw new Exception("[ERROR" . $lineNumber . "] Not Defined Command !! (" . $command . ")");
                    }
                    //  ----- コマンド処理
                    $this->commands[$command]->trancelate($lineParameters);
                    // 修飾コマンド
                    $subIndex = 0;
                    foreach ($lineParameters->getModifiers() as $decorate) {
                        //  ---- 修飾コマンド解析
                        $decorateCommand = Util/Parser::analyzeModifier($decorate);
                        //  ---- インデックス設定
                        $decorateCommand['id'] = $lineParameters->getId() . '_' . $subIndex;
                        //  ---- 修飾エイリアス確認
                        if (in_array($decorateCommand['command'], $this->decorationsAlias)) {
                            $decorateCommand['command'] = $this->decorationsAlias[$decorateCommand['command']];
                        }
                        //  ---- 修飾エイリアスになければ実態を確認
                        if (!in_array($decorateCommand['command'], $this->decorations)) {
                            throw new Exception("[ERROR:" . $lineNumber . "] Not Defined Command !! (" . $decorateCommand['command'] . ")");
                        }
                        //  ----- テキスト置換
                        $replaceData = $this->decorations[$decorateCommand['command']]->trancelate($decorateCommand);
                        $lineParameters->setText(str_replace($decorate, $replaceData['text'], $lineParameters->getText()));
                        //  ------ Script
                        if (!empty($replaceData['script'])) {
                            foreach ($replaceData['script'] as $key => $value) {
                                $this->decorateScript[$key] = array_merge($this->decorateScript[$key], $value);
                            }
                        }
                        //  ------ CSS
                        if (!empty($replaceData['css'])) {
                            $this->decorateCss[] = $replaceData['css'];
                        }
                        // サブインデックス更新
                        $subIndex += 1;
                    }
                    //  コンテナに格納
                    $this->vesselContainer[] = $lineParameters;
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

    /**
     * analyzeの結果を受けて、そのデータをHTML作成のために分散、収束を行う
     */
    public function transform(): void
    {
    }

    /**
     * transformの結果を受けて、HTMLファイルを作成する
     */
    public function genarate(): void
    {
    }

    public function getVesselContainer(): array
    {
        return $this->vesselContainer;
    }

    public function physicalTest(): string
    {
        return 'DONE';
    }
}
