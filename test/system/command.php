<?php

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
        // (ダミー) 作成するDocument Type を設定
        define('CURRENT_DOCUMENT_DATA', 'HTML4_LOOSE');

        // コマンド設定ファイルの読み込み確認
        $testJsonFile = __DIR__ . DIRECTORY_SEPARATOR  . 'test.json';
        $test = null;
        $error = null;
        try {
            $test = new Test($testJsonFile);
        } catch (Exception $e) {
            $error = $e;
        }
        $commandName = 'test';
        $tagOpen = 'test';
        $tagClose = '</test>';
        $blockType = 'ONE_LINE';
        $commandPerposes = ['HTML','CSS','SCRIPT'];
        $commandAlias = 'tst';
        $documentType = ['HTML4_LOOSE','HTML4_STRICT','XHTML1_LOOSE','HTML5','HTML5_1'];
        $tagAttributes = [
            'HTML4_LOOSE' => ['html4_loose_key' => 'html4_loose_value'],
            'HTML4_STRICT' => ['html4_strict_key' => 'html4_strict_value'],
            'XHTML1' => ['xhtml1_key' => 'xhtml1_value'],
            'HTML5' => ['html5_key' => 'html5_value'],
        ];
        $cssAttributes = ['css_key'=>'css_value'];

        $this->assertEquals($commandName, $test->getCommandName());
        $this->assertEquals($tagOpen, $test->getTagOpen());
        $this->assertEquals($tagClose, $test->getTagClose());
        $this->assertEquals($blockType, $test->getBlockTypeWithString());
        $this->assertEquals($commandPerposes, $test->getCommandPerposeWithString(), 0, 0, true);
        $this->assertEquals($commandAlias, $test->getCommandAlias());
        $this->assertEquals($documentType, $test->getPossibleDocumentTypesWithString(), 0, 0, true);
        $this->assertEquals($tagAttributes, $test->getPossibleTagAttributes(), 0, 0, true);
        $this->assertEquals($cssAttributes, $test->getPossibleCssAttributes(), 0, 0, true);
    }

    public function testFailedLoadSettingJsonFile()
    {
        // 設定ファイルの指定間違い確認
        $testJsonFile = __DIR__ . DIRECTORY_SEPARATOR  . 'this.json';
        $test = null;
        $error = null;
        try {
            $test = new Test($testJsonFile);
        } catch (Exception $e) {
            $error = $e;
        }
        $this->assertNotNull($error);
    }
}
