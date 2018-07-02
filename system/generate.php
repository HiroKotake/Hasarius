<?php

namespace Hasarius\system;

/**
 * HTML 生成クラス
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
    private $command_alias = [];
    /**
     * 修飾コマンド保持マップ
     * @var array
     */
    private $decorations = [];
    /**
     * 修飾コマンドエイリアス保持マップ
     * @var array
     */
    private $decorations_alias = [];

    function __construct()
    {
        $this->initialize();
    }

    /**
     * 初期設定実施
     */
    private function initialize() : void
    {
        // directory解析
        $dir_map = explode(DIRECTORY_SEPARATOR, __DIR__);
        array_pop($dir_map);
        $base_dir = implode(DIRECTORY_SEPARATOR, $dir_map);
        define('HASARIUS_BASE_DIR', $base_dir);
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

        // commands 読み込み
        $command_dir = dir(HASARIUS_COMMANDS_DIR);
        while (false !== ($file = $command_dir->read())) {
            if ($file != '.' || $file != '..') {
                // phpファイル読み込み
                require_once(HASARIUS_COMMANDS_DIR . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . $file . '.php');
                // クラス生成
                $this->commands[$file] = new $file();
                $this->$command_alias[$this->commands[$file]->ALIAS] = $file;
            }
        }
        $command_dir->close();

        // dcecoration 読み込み
        $decoration_dir = dir(HASARIUS_DECORATION_DIR);
        while (false !== ($file = $command_dir->read())) {
            if ($file != '.' || $file != '..') {
                // phpファイル読み込み
                require_once(HASARIUS_DECORATION_DIR . DIRECTORY_SEPARATOR . $file . $file . '.php');
                // クラス生成
                list($class_name, $exp) = explode('.', $file);
                $this->$decorations[$class_name] = new $class_name();
                $this->decorations_alias[$this->decorations[$class_name]->ALIAS] = $class_name;
            }
        }
        $decoration_dir->close();
    }

    /**
     * HTMLファイル生成
     * @param  string $source [description]
     * @return bool           [description]
     */
    function make(string $source) : bool
    {
        // 設定ファイル読み込み
        $source_path = explode(DIRECTORY_SEPARATOR, $source);
        array_pop($source_path);
        $make_config_file = implode(DIRECTORY_SEPARATOR, $source_path) . DIRECTORY_SEPARATOR . 'make.cfg';
        if (!file_exists($make_config_file)) {
            $make_config_file = 'HASARIUS_BASE_DIR' . DIRECTORY_SEPARATOR . 'make.cfg';
        }
        require_once($make_config_file);

        // 解析
        try {
            self::analyze($source);
        } catch (Exception $e){
            var_dump($e);
            return false;
        }

        return true;
    }

    function analyze(string $source, $line_number = 0) : int
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
                $line_number++;    // 行インデックス更新
                //  --- 解析
                $line_parameters = Parser::analyze_line($line);
                if ($line_parameters['command'] == 'include') {
                    // --- 外部ソース読み込み
                    self::analyze($line_parameters['text'], $line_number);
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
            echo 'Error at line nunber : ' . $line . PHP_EOL;
            throw $e;
        }

        return $line;
    }

}
