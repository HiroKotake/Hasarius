<?php

namespace Hasarius\system;

class MakeConst
{

    public static function load()
    {
        $json = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'const.json');
        $constBases = json_decode($json, true);

        foreach ($constBases as $key => $info) {
            if (!defined($key)) {
                define($key, $info['value']);
            }
        }
    }
}
