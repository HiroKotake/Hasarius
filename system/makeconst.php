<?php
/**
 * makeconst.php
 *
 * @package Hasarius
 * @category system
 * @author Takahiro Kotake
 * @license Teleios Development
 */

namespace Hasarius\system;

class MakeConst
{

    public static function load(string $file = null): void
    {
        $file = $file ?? __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'const.json';
        $json = file_get_contents($file);
        $constBases = json_decode($json, true);
        foreach ($constBases as $key => $info) {
            if (!defined($key)) {
                define($key, $info);
            }
        }
    }

    public static function loadMakeFile(string $file): void
    {
        if (!file_exists($file)) {
            throw new Exception("[ERROR] make.json file is not existed !!", 1);
        }

        $json = file_get_contents($file);
        $constBases = json_decode($json, true);
        foreach ($constBases as $key => $info) {
            $makeKey = "MAKE_" . $key;
            if (!defined($makeKey)) {
                define($makeKey, $info);
            }
        }
    }
}
