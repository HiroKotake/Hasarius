<?php

namespace Hasarius;

$src = "";
$dist = "";
$title = "";
$fname = "";
$params = [];
// 引数チェック
if ($argc == 1) {
    // ソース指定なし
    showCommandLine();
    return 1;
} elseif ($argc == 2) {
    // ソース指定のみ
    $src = $argv[1];
    $params["Source"] = $src;
} else {
    foreach ($argv as $pram) {
        // ソース (src)
        $match = null;
        if (preg_match('/^src=\"?(.*)\"?/ui', $pram, $match) != 0) {
            $src = $match[1];
            $params["Source"] = $src;
            continue;
        }
        // 保存先 (dist)
        if (preg_match('/^dist=\"?(.*)\"?/ui', $pram, $match) != 0) {
            $dist = $match[1];
            $params["DestDir"] = $dist;
            continue;
        }
        // ファイル名
        if (preg_match('/^fname=\"?(.*)\"?/ui', $pram, $match) != 0) {
            $fname = $match[1];
            $params["DestFile"] = $fname;
            continue;
        }
        // タイトル (title)
        if (preg_match('/^title=\"?(.*)\"?/ui', $pram, $match) != 0) {
            $title = $match[1];
            $params["Title"] = $title;
            continue;
        }
        // 変数指定
        if (preg_match('/^(@[a-zA-Z0-9_-]+)=\"?(.*)\"?/', $pram, $match) != 0) {
            $params[$match[1]] = $match[2];
        }
    }
}

if (empty($src)) {
    echo PHP_EOL . "[ERROR] Source file is empty !!" . PHP_EOL;
    showCommandLine();
    return 1;
}

if (!file_exists($src) || !is_file($src)) {
    echo PHP_EOL . "[ERROR] Source file is not exists !!" . PHP_EOL;
    echo "    - " . $src . PHP_EOL;
    showCommandLine();
    return 1;
}

if (!empty($dist) && (!file_exists($dist) || !is_dir($dist))) {
    echo PHP_EOL . "[ERROR] Dist is not exists or not directory !!" . PHP_EOL;
    echo "    - " . $dist . PHP_EOL;
    showCommandLine();
    return 1;
}

// HTML生成
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'autoloader.php');
$autoload = new \Hasarius\AutoLoader();
$autoload->autoload();

$genarate = new \Hasarius\system\Generate();
//$genarate->make(["Source" => $src, "DestDir" => $dist, "Title" => $title]);
$result = $genarate->make($params);
if ($result) {
    echo "Done !!" . PHP_EOL;
}

function showCommandLine()
{
    echo PHP_EOL;
    echo "\t1)\tphp hasarius.php [source file]" . PHP_EOL;
    echo "\t2)\tphp hasarius.php src=[source file]" . PHP_EOL;
    echo PHP_EOL;
    echo "\t option:" . PHP_EOL;
    echo "\t src   : source file" . PHP_EOL;
    echo "\t dist  : generated html file's distination" . PHP_EOL;
    echo "\t fname : new filename" . PHP_EOL;
    echo "\t title : html's page title" . PHP_EOL;
    echo PHP_EOL;
}
