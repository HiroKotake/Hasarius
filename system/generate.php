<?php

namespace Hasarius\system;

/**
 * [Genarate description]
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
        define('HASARIUS_SYSTEM_DIR', $base_dir . DIRECTORY_SEPARATOR . 'system');
        define('HASARIUS_COMMANDS_DIR', $base_dir . DIRECTORY_SEPARATOR . 'commands');
        define('HASARIUS_DECORATION_DIR', $base_dir . DIRECTORY_SEPARATOR . 'decoration');

        // commands読み込み
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

        // dcecoration読み込み
        $decoration_dir = dir(HASARIUS_DECORATION_DIR);
        while (false !== ($file = $command_dir->read())) {
            if ($file != '.' || $file != '..') {
                // phpファイル読み込み
                require_once(HASARIUS_DECORATION_DIR . DIRECTORY_SEPARATOR . $file . $file . '.php');
                // クラス生成
                list($class_name, $exp) = explode('.', $file);
                $this->$decorations[$class_name] = new $class_name();
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
        // 解析
        //  - ファイルオープン
        //  -- 行読み込み
        //  --- 解析
        // 出力
    }

}
