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
        $file = $file ?? __DIR__ . DIRECTORY_SEPARATOR . 'const.json';
        $json = file_get_contents($file);
        $constBases = json_decode($json, true);
        foreach ($constBases as $key => $info) {
            if (!defined($key)) {
                define($key, $info);
            }
        }
    }
}
