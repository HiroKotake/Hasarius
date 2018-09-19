<?php

namespace Hasarius;

$src = "";
$dist = "";
$title = "";

// 引数チェック
if ($argc == 1) {
    // ソース指定なし
    showCommandLine();
    return 1;
} elseif ($argc == 2) {
    // ソース指定のみ
    $src = $argv[1];
} else {
    foreach ($argv as $pram) {
        // ソース (src)
        $match = null;
        if (preg_match('/^src=\"?(.*)\"?/ui', $pram, $match) != 0) {
            $src = $match[1];
        }
        // 保存先 (dist)
        if (preg_match('/^dist=\"?(.*)\"?/ui', $pram, $match) != 0) {
            $dist = $match[1];
        }
        // タイトル (title)
        if (preg_match('/^title=\"?(.*)\"?/ui', $pram, $match) != 0) {
            $title = $match[1];
        }
    }
}

if (empty($src)) {
    showCommandLine();
    return 1;
}


// HTML生成
require_one('./autoloader.php');
$autoload = new \Hasarius\AutoLoader();
$autoload->autoload();

$genarate = new Hasarius\system\Genarate();
$genarate->make(["Source" => $src, "Destination" => $dist, "title" => $title]);

function showCommandLine()
{
    echo PHP_EOL;
    echo "\t1)\tphp hasarius.php [source file]" . PHP_EOL;
    echo "\t2)\tphp hasarius.php src=[source file]" . PHP_EOL;
    echo PHP_EOL;
    echo "\t option:" . PHP_EOL;
    echo "\t src   : source file" . PHP_EOL;
    echo "\t dist  : generated html file's distination" . PHP_EOL;
    echo "\t title : html's page title" . PHP_EOL;
    echo PHP_EOL;
}
