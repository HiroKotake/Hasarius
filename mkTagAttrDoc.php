<?php

namespace Hasarius;

if ($argc < 3) {
    echo PHP_EOL;
    echo "-- タグ関連ドキュメント雛形生成";
    echo "php mkTagAttrDoc.php <対象のタグ> <ファイル保存先>" . PHP_EOL;
    echo PHP_EOL;
    return;
}

$targetTagName = $argv[1];
$distnationDir = $argv[2];

$filename = __DIR__ . DIRECTORY_SEPARATOR . $targetTagName . DIRECTORY_SEPARATOR . "define.json";
if (!file_exists($filename)) {
    echo PHP_EOL . "Tag is not exists !!" . PHP_EOL;
    return;
}

if (!file_exists($distnationDir) || !is_dir($distnationDir)) {
    echo PHP_EOL . "Distination directory is not exists !!" . PHP_EOL;
    return;
}
$wFilename = $distnationDir . DIRECTORY_SEPARATOR . str_replace("/", "_", $targetTagName) . ".txt";

$json = file_get_contents($filename);
$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$settings = json_decode($json, true);

$tagData = new TagData();
$tagData->name = $settings["CommandName"];
$tagData->alias = $settings["CommandAlias"];
$tagData->setPossibleDocType($settings["DocumentType"]);
$tagData->blockType = $settings["BlockType"];
// Attribute
foreach ($settings["TagAttributes"] as $format => $attributes) {
    foreach ($attributes as $attNmae => $data) {
        $tagData->setTagAttribute($attNmae, $format, $data);
    }
}
// Custom Attribute
foreach ($settings["CustomAttributes"] as $attrName => $data) {
    $tagData->setCustomAttribute($attrName, $data);
}
// Sub Command
if (array_key_exists("SubCommand", $settings)) {
    $tagData->setSubCommand($settings["SubCommand"]);
}

// 雛形作成
$line = [];
$line[] = $tagData->name . PHP_EOL;
$line[] = "#hr" . PHP_EOL;
$line[] = "#h2 用途" . PHP_EOL;
$line[] = "ブロックタイプ： " . $tagData->blockType . PHP_EOL;
$line[] = "" . PHP_EOL;
$line[] = "#h2 利用可能なHTMLのバージョン" . PHP_EOL;
$line[] = "このタグの利用可能なHTMLのバージョンは以下のものです。" . PHP_EOL;
$line[] = '#pre' . PHP_EOL;
$line[] = $tagData->possibleDocType . PHP_EOL;
$line[] = "##" . PHP_EOL;
// 属性について
$line[] = "#h2 属性について" . PHP_EOL;
if (empty($tagData->attribute)) {
    $line[] = "このタグではHTMLで定義されているグローバル属性のみ使用可能です。" . PHP_EOL;
} else {
    $line[] = "このタグではHTMLで定義されているグローバル属性以外に以下の属性が使用可能です。" . PHP_EOL;
    $line[] = '#tbl' . PHP_EOL;
    $line[] = "+" . PHP_EOL;
    $line[] = "! 属性名" . PHP_EOL;
    $line[] = "! 属性値" . PHP_EOL;
    foreach ($settings["DocumentType"] as $dtype) {
        $line[] = "! $dtype" . PHP_EOL;
    }
    $line[] = "! 説明" . PHP_EOL;
    $line[] = "##" . PHP_EOL;
    foreach ($tagData->attribute as $attrName => $data) {
        $line[] = "+" . PHP_EOL;
        $line[] = "| " . $attrName . PHP_EOL;
        $line[] = "| " . $data->value . PHP_EOL;
        foreach ($settings["DocumentType"] as $dtype) {
            $line[] = "|" . ($data->target[$dtype] == 0 ? "×" : ($data->target[$dtype] == 1 ? "〇" : ($data->target[$dtype] == 2 ? "△" : "▲"))) . PHP_EOL;
        }
        $line[] = "| <説明>" . PHP_EOL;
        $line[] = "##" . PHP_EOL;
    }
    $line[] = "##" . PHP_EOL;
    $line[] = '"×" ... 使用不可' . PHP_EOL;
    $line[] = '"〇" ... 必須' . PHP_EOL;
    $line[] = '"△" ... 任意' . PHP_EOL;
    $line[] = '"▲" ... 非推奨' . PHP_EOL;
    $line[] = PHP_EOL;
}
// サブコマンド
if (!empty($tagData->subCommand)) {
    $line[] = "#h2 サブコマンドについて" . PHP_EOL;
    $line[] = "このタグにはサブコマンドが設定されていますので、特別に短縮したタグを使用することができます。" . PHP_EOL;
    $line[] = '通常はタグコマンドであることを示すために行頭に"#<タグ名>"を設定しますが、サブコマンドが設定されている場合はサブコマンドのみでタグ名を指定をする必要はありません。' . PHP_EOL;
    $line[] = 'サブコマンドには以下のものが定義されています。' . PHP_EOL;
    $line[] = PHP_EOL;
    $line[] = "#tbl" . PHP_EOL;
    $line[] = "+" . PHP_EOL;
    $line[] = "! サブコマンド" . PHP_EOL;
    $line[] = "! 対応するタグ" . PHP_EOL;
    $line[] = "! 説明" . PHP_EOL;
    $line[] = "##" . PHP_EOL;
    foreach ($tagData->subCommand as $name => $data) {
        $line[] = "+" . PHP_EOL;
        $line[] = "| " . $name . PHP_EOL;
        $line[] = "| " . $data["Tag"] . PHP_EOL;
        $line[] = "| " . $data["Description"] . PHP_EOL;
        $line[] = "##" . PHP_EOL;
    }
    $line[] = "##" . PHP_EOL;
}
$line[] = "" . PHP_EOL;
$line[] = "#h2 サンプル" . PHP_EOL;
$line[] = "" . PHP_EOL;


$hFile = fopen($wFilename, "w");
foreach ($line as $str) {
    fwrite($hFile, $str);
}
fclose($hFile);

echo "DONE !!" . PHP_EOL;






/*********************************************************************************************************************/
/* Utility Class                                                                                                     */
/*********************************************************************************************************************/
class TagAttributesTarget
{
    public $value = "";
    public $target = [
        "HTML4_LOOSE"   => 0,   // 0:設定なし 1:設定あり（必須） 2:設定あり（任意）3:非推奨
        "HTML4_STRICT"  => 0,
        "HTML4_FRAME"   => 0,
        "XHTML1_LOOSE"  => 0,
        "XHTML1_STRICT" => 0,
        "XHTML1_FRAME"  => 0,
        "XHTML1_1"      => 0,
        "HTML5"         => 0,
        "HTML5_1"       => 0,
    ];

    public function setTargetType(string $format, string $priority): void
    {
        $this->target[$format] = $priority == "OPTION" ? 2 : ($priority == "DEPRECATION" ? 3 : 1);
    }
}

class TagData
{
    public $name = "";
    public $alias = "";
    public $possibleDocType = "";
    public $blockType = "";
    public $attribute = [];
    public $subCommand = [];

    public function setPossibleDocType(array $docTypes): void
    {
        foreach ($docTypes as $dtype) {
            $this->possibleDocType .= " " . $dtype . ",";
        }
        $this->possibleDocType = "   " . rtrim($this->possibleDocType, ",");
    }

    public function setTagAttribute(string $attrName, string $format, array $tagData): void
    {
        if (array_key_exists($attrName, $this->attribute)) {
            $this->attribute[$attrName]->setTargetType($format, $tagData["Priority"]);
        } else {
            $this->attribute[$attrName] = new TagAttributesTarget();
            $this->attribute[$attrName]->value = $tagData["Value"];
            $this->attribute[$attrName]->setTargetType($format, $tagData["Priority"]);
        }
    }

    public function setCustomAttribute(string $attrName, array $tagData): void
    {
        if (!array_key_exists($attrName, $this->attribute)) {
            $this->attribute[$attrName] = new TagAttributes();
            $this->attribute[$attrName]->value = $tagData["Value"];
            foreach ($tagData["DocumentType"] as $format) {
                $this->attribute[$attrName]->setTargetType($format, $tagData["Priority"]);
            }
        }
    }

    public function setSubCommand(array $subCommands): void
    {
        foreach ($subCommands as $sub) {
            $this->subCommand[$sub["Symbol"]] = [
                "Tag" => $sub["Tag"],
                "Description" => $sub["Description"],
            ];
        }
    }
}
