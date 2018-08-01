<?php
namespace Hasarius\test\system;

use Hasarius\system\Command;
use PHPUnit\Framework\TestCase;

/**
 * Commandクラス確認用クラス
 */
class Test extends Command
{
    public function __construct(string $filepath)
    {
        parent::__construct($filepath);
    }
}

//class TestCommand extends TestCase
class TestCommand extends TestCase
{

    public function testLoadSettingJsonFile()
    {
        // コマンド設定ファイルの読み込み確認
        $testJsonFile = __DIR__ . DIRECTORY_SEPARATOR  . 'test.json';
        $test = null;
        try {
            $test = new Test($testJsonFile);
        } catch (\Exception $e) {
            echo PHP_EOL;
            echo "[ERROR] ". $e->getMessage() . PHP_EOL;
            echo PHP_EOL;
            return;
        }
        $commandName = 'test';
        $tagOpen = 'test';
        $tagClose = '</test>';
        $blockType = 'INLINE';
        $commandPerposes = ['HTML','CSS','SCRIPT'];
        $commandAlias = 'tst';
        $documentType = ['HTML4_LOOSE','HTML4_STRICT','XHTML1_LOOSE','HTML5','HTML5_1'];
        $tagAttributes = [
            'HTML4_LOOSE' => ['html4_loose_key' => 'html4_loose_value'],
            'HTML4_STRICT' => ['html4_strict_key' => 'html4_strict_value'],
            'XHTML1' => ['xhtml1_key' => 'xhtml1_value'],
            'HTML5' => ['html5_key' => 'html5_value'],
        ];
        $customAttributes = [
            "custom_key" => [
                "alias" => "ckey",
                "type" => "string"
            ]
        ];
        $subCommand = [
            ["Symbol" => "+", "DefaultAttrs" => [], "Description" => "行指定"],
            ["Symbol" => "!", "DefaultAttrs" => [], "Description" => "見出しカラム指定"],
            ["Symbol" => "|", "DefaultAttrs" => [], "Description" => "カラム指定"],
        ];

        $this->assertEquals($commandName, $test->getCommandName());
        $this->assertEquals($tagOpen, $test->getTagOpen());
        $this->assertEquals($tagClose, $test->getTagClose());
        $this->assertEquals($blockType, $test->getBlockTypeWithString());
        $this->assertEquals($commandPerposes, $test->getCommandPerposeWithString(), 0, 0, true);
        $this->assertEquals($commandAlias, $test->getCommandAlias());
        $this->assertEquals($documentType, $test->getPossibleDocumentTypesWithString(), 0, 0, true);
        $this->assertEquals($tagAttributes, $test->getPossibleTagAttributes(), 0, 0, true);
        $this->assertEquals($customAttributes, $test->getPossibleCustomAttributes(), 0, 0, true);
        $this->assertEquals($subCommand, $test->getSubCommand(), 0, 0, true);
    }

    public function testFailedLoadSettingJsonFile()
    {
        // 設定ファイルの指定間違い確認
        $testJsonFile = __DIR__ . DIRECTORY_SEPARATOR  . 'this.json';
        $test = null;
        try {
            $test = new Test($testJsonFile);
        } catch (\Exception $e) {
            $this->assertEquals('File Not Found !! - ' . $testJsonFile, $e->getMessage());
        }
    }
}
