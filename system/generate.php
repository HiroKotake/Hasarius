<?php
/**
 * genarate.php
 *
 * @package Hasarius
 * @category system
 * @author Takahiro Kotake
 * @license Teleios Development
 */

namespace Hasarius\system;

use Hasarius\utils as Utils;
use Hasarius\preprocess as Preprocess;
use Hasarius\command as Command;
use Hasarius\decorate as Decorate;

/**
 * HTML 生成クラス
 *
 * @package hasarius
 * @category system
 * @author Takahiro Kotake
 */
class Generate
{
    /**
     * 設定値を格納
     * @var array
     */
    private $config = [];
    /**
     * プリプロセスコマンド保持マップ
     * @var array
     */
    private $preprocessCommand = [];
    /**
     * プリプロセスコマンドエイリアス保持マップ
     * @var array
     */
    private $preprocessCommandAlias = [];
    /**
     * コマンド保持マップ
     * @var array
     */
    private $commands = [];
    /**
     * コマンドエイリアス保持マップ
     * @var array
     */
    private $commandAlias = [];
    /**
     * 修飾コマンド保持マップ
     * @var array
     */
    private $decorations = [];
    /**
     * 修飾コマンドエイリアス保持マップ
     * @var array
     */
    private $decorationsAlias = [];
    /**
     * クローズスタック
     * @var string
     */
    private $closerStack = [];
    /**
     * 現在有効なサブコマンド
     * @var array
     */
    private $currentSubCommand = null;
    /**
     * コンテナ
     * @var array
     */
    private $vesselContainer = [];

    /**
     * スクリプトコンテナ
     * @var array
     */
    private $scriptStack = [
        'fileReady' => [],
        'file'      => [],
        'headReady' => [],
        'head'      => [],
    ];

    /**
     * スタイルシートコンテナ
     * @var array
     */
    private $cssStack = [];

    /**
     * 現在のインデント数
     * @var int
     */
    private $currentIndent = 1; // BODYタグは自動生成となるので、スタートは１から。

    /**
     * ドキュメント生成ワーク
     * @var array
     */
    private $documentWork = [];

    /**
     * パラメータ検証エラー一覧
     * @var array
     */
    private $validateErrorList = [];

    public function __construct()
    {
        $this->currentSubCommand = new CloseInfo();
        $this->initialize();
    }

    /**
     * システム初期設定実施：ユーザ指定の設定以外のシステム側だけの設定を読み込む
     */
    private function initialize() : void
    {
        // 定数値読み込み
        MakeConst::load();

        // directory解析
        $dirMap = explode(DIRECTORY_SEPARATOR, __DIR__);
        array_pop($dirMap);
        $baseDir = implode(DIRECTORY_SEPARATOR, $dirMap);
        define('HASARIUS_BASE_DIR', $baseDir);
        define('HASARIUS_PREPROCESS_DIR', HASARIUS_BASE_DIR . DIRECTORY_SEPARATOR . 'preprocess');
        define('HASARIUS_SYSTEM_DIR', HASARIUS_BASE_DIR . DIRECTORY_SEPARATOR . 'system');
        define('HASARIUS_UTILS_DIR', HASARIUS_BASE_DIR . DIRECTORY_SEPARATOR . 'utils');
        define('HASARIUS_COMMANDS_DIR', HASARIUS_BASE_DIR . DIRECTORY_SEPARATOR . 'commands');
        define('HASARIUS_DECORATION_DIR', HASARIUS_BASE_DIR . DIRECTORY_SEPARATOR . 'decorations');

        // preprocess 読み込み
        $commandDir = dir(HASARIUS_PREPROCESS_DIR);
        while (false !== ($file = $commandDir->read())) {
            if (($file != '.' || $file != '..') && is_dir($file)) {
                // クラス生成
                $className = 'Preprocess\Preprocess' . ucfirst($file);
                $this->preprocessCommand[$file] = new $className();
                $this->preprocessCommandAlias[$this->preprocessCommand[$file]->getCommandAlias()] = $file;
            }
        }
        $commandDir->close();

        // commands 読み込み
        $commandDir = dir(HASARIUS_COMMANDS_DIR);
        while (false !== ($file = $commandDir->read())) {
            if (($file != '.' || $file != '..') && is_dir($file)) {
                $filepath = HASARIUS_COMMANDS_DIR . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR;
                // クラス生成
                if (file_exists($filepath . $file . '.php')) {
                    // PHPファイルの定義がある場合
                    $className = 'Command\Command' . ucfirst($file);
                    $this->commands[$file] = new $className();
                } else {
                    // JSONファイル定義が主体
                    $this->commands[$file] = new Command($filepath . 'define.json');
                }
                $this->commandAlias[$this->commands[$file]->getCommandAlias()] = $file;
            }
        }
        $commandDir->close();

        // dcecoration 読み込み
        $decorationDir = dir(HASARIUS_DECORATION_DIR);
        while (false !== ($file = $decorationDir->read())) {
            if (($file != '.' || $file != '..') && is_dir($file)) {
                $filepath = HASARIUS_DECORATION_DIR . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR;
                // クラス生成
                if (file_exists($filepath . $file . '.php')) {
                    // PHPファイルの定義がある場合
                    $className = 'Decorate\Decorate' . ucfirst($file);
                    $this->decorations[$file] = new $className();
                } else {
                    // JSONファイル定義が主体
                    $this->decorations[$file] = new $className($filepath . 'define.json');
                }
                $this->decorationsAlias[$this->decorations[$file]->getCommandAlias()] = $file;
            }
        }
        $decorationDir->close();
    }

    /**
     * HTMLファイル生成
     * @param  string $source 元ファイル
     * @return bool           成功時には真を、失敗時に偽を返す
     */
    public function make(string $source): bool
    {
        // HTMLファイル生成用ユーザ独自の設定ファイル読み込み
        $sourcePath = explode(DIRECTORY_SEPARATOR, $source);
        array_pop($sourcePath);
        $makeConfigFile = implode(DIRECTORY_SEPARATOR, $sourcePath) . DIRECTORY_SEPARATOR . 'make.json';
        if (!file_exists($makeConfigFile)) {
            // ユーザ指定がなければシステム側提供の設定ファイルを使用
            $makeConfigFile = 'HASARIUS_BASE_DIR' . DIRECTORY_SEPARATOR . 'make.json';
        }
        $json = file_get_contents($makeConfigFile);
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $this->config = json_decode($json, true);

        // HTML生成
        try {
            // ToDo: プリプロセス処理を追加（定数対応、変数対応、プリプロセス用バッチコマンド対応)
            // プリプロセス
            $lines = $this->preprocess($source);
            // 解析
            $this->analyze($lines);
            // 構築
            $this->transform();
            // ファイル出力
            $this->genarate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }

        return true;
    }

    /**
     * ファイルを読み込み事前準備を実施する
     * @param  string $source ソースファイル
     * @return array          事前準備完了後のデータ配列
     */
    public function preprocess(string $source): array
    {
        $lineNumber = 0;
        $lines = [];
        $variables = [];
        $sourceInfo = explode($source, DIRECTORY_SEPARATOR);
        $filename = array_pop($sourceInfo);
        try {
            // ファイル存在確認
            if (!file_exists($source)) {
                throw new \Exception("[ERROR] FILE NOT EXISTS !!", 1);
            }
            // ファイルオープン
            $hFile = fopen($source, 'r');
            // 行読み込み
            while (($line = fgets($hFile)) !== false) {
                // 行カウントアップ
                $lineNumber++;
                // 変数置き換え
                if (empty($variables)) {
                    // 変数定義がある場合に置き換え処理を実施
                    $line = Parser::replaceVariable($variables, $line);
                }
                // 外部ソースチェック & 読み込み
                $match = null;
                $checkedSource = Parser::getIncludeFile($line);
                if (!empty($checkedSource["filename"])) {
                    $lines = array_merge($lines, $this->preprocess($checkedSource["filename"]));
                    continue;
                }
                // 変数対応
                $match = null;
                $tempVar = Parser::getValiable($line);
                if (array_key_exists($tempVar['valName'], $variables)) {
                    throw new \Exception("Deplicate variable error !!");
                }
                if (!empty($tempVar['valName'])) {
                    $variables[$tempVar['valName']] = $tempVar;
                    continue;
                }
                // 特殊コマンド対応
                $vessel = Parser::analyzeLine($line, [], '@', '=');
                if (!empty($vessel->getCommand())) {
                    $batch = $vessel->getBatch();
                    foreach ($batch as $batchLine) {
                        $line[] = [
                            'filename' => $filename,
                            'filefullpath' => $source,
                            'lineNumber' => $lineNumber,
                            'lineText' => $batchLine,
                        ];
                    }
                    continue;
                }
                // ライン読み込み
                $lines[] = [
                    'filename' => $filename,
                    'filefullpath' => $source,
                    'lineNumber' => $lineNumber,
                    'lineText' => $line,
                ];
            }
            // ファイルクローズ
            fclose($hFile);
        } catch (\Exception $e) {
            throw new Exception('[ERROR:PREPROCESS] ' . $filename . ':' . $lineNumber . ' - ' . $e->getMessage(), 1);
        }
        return $lines;
    }

    /**
     * 指定されたHasariusテキストファイルを解析
     * （再帰呼び出しあり）
     *
     * @param  array $source     プリプロセスを経由して作成されたデータ配列
     */
    public function analyze(array $source) : int
    {
        // STEP 1 : READ LINE
        try {
            //  -- 行読み込み
            foreach ($source as $line) {
                // ToDo: 文字エンコード変換
                //  --- 解析
                $lineParameters = Utils\Parser::analyzeLine($line['lineText'], $this->currentSubCommand->getSubCommand());
                if (is_numeric($lineParameters->getCommand())) {
                    // --- システムコマンド系
                    //  コンテナに格納
                    $this->vesselContainer[] = $lineParameters;
                } elseif ($lineParameters->isSubCommand()) {
                    // サブコマンド指定 -> 親コマンドに処理を任せる
                    $this->commands[$this->currentSubCommand->getCommand()]->execSubCommand($lineParameters);
                } else {
                    $command = $lineParameters->getCommand();
                    //  ---- コマンドエイリアス確認
                    if (in_array($command, $this->commandAlias)) {
                        $command = $this->commandAlias[$command];
                    }
                    //  ---- コマンドエイリアスになければ実態を確認
                    if (!in_array($command, $this->commands)) {
                        throw new \Exception('[ERROR:ANALYZE] ' . $line['filename']. ':' . $line['lineNumber'] . ' - ' . 'Not Defined Command !! (' . $command . ')');
                    }
                    //  ----- パラメータ検証
                    $validateResult = Utils\HtmlValidation($this->commands[$command], $lineParameters->getParamaters());
                    if (!empty($validateResult)) {
                        if (HEAD_ValidateStop) {
                            throw new \Exception('[ERROR:VALIDATE] ' . $line['filename'] . ':' . $line['lineNumber'] . PHP_EOL . $validateResult);
                        } else {
                            $this->validateErrorList = array_merge($this->validateErrorList, $validateResult);
                        }
                    }
                    //  ----- コマンド処理
                    $this->commands[$command]->trancelate($lineParameters);
                    // 修飾コマンド
                    $subIndex = 0;
                    foreach ($lineParameters->getModifiers() as $decorate) {
                        //  ---- 修飾コマンド解析
                        $decorateCommand = Util/Parser::analyzeModifier($decorate);
                        //  ---- インデックス設定
                        $decorateCommand['id'] = $lineParameters->getId() . '_' . $subIndex;
                        //  ---- 修飾エイリアス確認
                        if (in_array($decorateCommand['command'], $this->decorationsAlias)) {
                            $decorateCommand['command'] = $this->decorationsAlias[$decorateCommand['command']];
                        }
                        //  ---- 修飾エイリアスになければ実態を確認
                        if (!in_array($decorateCommand['command'], $this->decorations)) {
                            throw new \Exception('[ERROR:ANALYZE] ' . $line['filename']. ':' . $line['lineNumber'] . ' - ' . 'Not Defined Command !! (' . $decorateCommand['command'] . ')');
                        }
                        //  ----- パラメータ検証
                        $validateResult = Utils\HtmlValidation($this->decorations[$decorateCommand], $decorateCommand['params']);
                        if (!empty($validateResult)) {
                            if (HEAD_ValidateStop) {
                                throw new \Exception('[ERROR:VALIDATE] ' . $line['filename'] . ':' . $line['lineNumber'] . PHP_EOL . $validateResult);
                            } else {
                                $this->validateErrorList = array_merge($this->validateErrorList, $validateResult);
                            }
                        }
                        //  ----- テキスト置換
                        $replaceData = $this->decorations[$decorateCommand['command']]->trancelate($decorate);
                        $lineParameters->setText(str_replace($decorate, $replaceData['text'], $lineParameters->getText()));
                        //  ------ Script
                        if (!empty($replaceData['script'])) {
                            foreach ($replaceData['script'] as $key => $value) {
                                $this->scriptStack[$key] = array_merge($this->scriptStack[$key], $value);
                            }
                        }
                        //  ------ CSS
                        if (!empty($replaceData['css'])) {
                            $this->cssStack[] = $replaceData['css'];
                        }
                        // サブインデックス更新
                        $subIndex += 1;
                    }
                    //  コンテナに格納
                    $this->vesselContainer[] = $lineParameters;
                }
            }
        } catch (\Exception $e) {
            throw new \Exception('[ERROR:ANALYZE] ' . $line['filename']. ':' . $line['lineNumber'] . ' - ' . $e->getMessage());
        }
    }

    /**
     * analyzeの結果を受けて、そのデータをHTML作成のために分散、収束を行う
     */
    public function transform(): void
    {
        foreach ($this->vesselContainer as $vessel) {
            // スクリプト抽出
            $workScript = $vessel->getScript();
            if (!empty($workScript)) {
                foreach ($workScript as $key => $val) {
                    $this->scriptStack[$key] = array_merge($this->scriptStack[$key], $val);
                }
            }
            // CSS抽出
            $workCss = $vessel->getCss();
            if (!empty($workCss)) {
                $this->cssStack[] = $workCss;
            }
            // ブロックコマンド抽出
            $vessel->setIndent($this->currentIndent);
            if ($vessel->getBlockType() == BaseTag::BLOCK_TYPE_BLOCK) {
                // 現在のサブコマンドを設定する
                $this->currentSubCommand = new CloserInfo($vessel->getTagClose(), $vessel->getSubCommand(), $vessel->getAutoIndent());
                array_push($this->closerStack, $this->currentSubCommand);
                // インデント数
                $this->currentIndent += 1;
            }
        }
    }

    /**
     * transformの結果を受けて、HTMLファイルを作成する
     */
    public function genarate(): void
    {
        // スクリプトファイルを生成
        $scriptFileName = $this->subGenarateScriptFile();
        // CSSファイルを生成
        $cssFileName = $this->subGenarateCssFile();
        // 設定からヘッダ部を生成
        $this->subGenarateHead($scriptFileName, $cssFileName);
        // BODY部を生成
        $this->subGenarateBody();
        // HTMLファイル出力
        $fileName = DESTINATION . DIRECTORY_SEPARATOR . TARGET_NAME . '.html';
        $hFile = fopen($fileName, 'w');
        foreach ($this->documentWork as $line) {
            fwrite($hFile, $line . PHP_EOL);
        }
        fclose($hFile);
    }

    private function subGenarateScriptFile(): string
    {
        $scriptFileName = "";
        if (!empty($this->scriptStack['FILE']) || !empty($this->scriptStack['FILE_READY'])) {
            $scriptFileName = DESTINATION . DIRECTORY_SEPARATOR . TARGET_NAME . '.js';
            $hFile = fopen($scriptFileName, "w");

            // 初期起動用スクリプト定義
            if (!empty($this->scriptStack['FILE_READY'])) {
                $scriptOpen  = "";
                $scriptClose = "";
                switch (SCRIPT_FRAMEWORK) {
                    case 'JQuery':
                        $scriptOpen  = SCRIPT['JQuery']['READY']['Open'];
                        $scriptClose = SCRIPT['JQuery']['READY']['Close'];
                        break;
                    case 'None':
                    default:
                        $scriptOpen  = SCRIPT['None']['READY']['Open'];
                        $scriptClose = SCRIPT['None']['READY']['Close'];
                        break;
                }
            }

            // 初期起動用スクリプト書き出し
            fwrite($hFile, $scriptOpen . PHP_EOL);
            foreach ($this->scriptStack['FILE_READY'] as $line) {
                fwrite($hFile, $line . PHP_EOL);
            }
            fwrite($hFile, $scriptClose . PHP_EOL);

            // JavaScript通常関数書き出し
            foreach ($this->scriptStack['FILE'] as $line) {
                fwrite($hFile, $line . PHP_EOL);
            }
            fclose($hFile);
        }
        return $scriptFileName;
    }

    private function subGenarateCssFile(): string
    {
        $cssFileName = "";
        if (!empty($this->cssStack)) {
            $cssFileName = DESTINATION . DIRECTORY_SEPARATOR . TARGET_NAME . '.css';
            $hFile = fopen($cssFileName, 'w');
            foreach ($this->cssStack as $line) {
                fwrite($hFile, $line . PHP_EOL);
            }
            fclose($hFile);
        }
        return $cssFileName;
    }

    private function subGenarateHead(string $scriptFile, string $cssFile): void
    {
        // 設定からヘッダ部(設定ファイルによる部分のみ)を生成
        $this->documentWork[] = '<html>';
        $this->documentWork[] = '    <head>';

        // 設定からヘッダ部(コマンドにより生成されたスクリプト,CSS)を生成
        $this->documentWork[] = '        <script type="text/javascript" src="' .$scriptFile . '"></script>';        // ToDo: 設定として個別の排出先指定が可能なようにする
        $this->documentWork[] = '        <link rel="stylesheet" type="text/css" href="' . $cssFile . '"></style>';  // ToDo: 設定として個別の排出先指定が可能なようにする
        $this->documentWork[] = '    </head>';
    }

    // ToDo: インデントは無視している
    private function subGenarateBody(): void
    {
        foreach ($this->vesselContainer as $vessel) {
            // インデント分生成
            if ($vessel->getAutoIndent()) {
                $indentText = Utils\indentRepeat($vessel->getIndent());
            }
            // コメント存在確認
            // * 設定ファイルによりコメント表示がONならばコメントテキストを追加する
            $comment = "";
            if (SHOW_COMMENT && !empty($vessel->getComment())) {
                $comment = ' <!-- ' . $vessel->getComment() . ' -->';
            }
            // ブロッククローズ
            if ($vessel->getCommand() == Parser::SYSTEM_BLOCK_CLOSE) {
                // 終了タグをスタックから取り込む
                $closeVessel = array_pop($this->closerStack);
                $this->documentWork[] = $indentText . $closeVessel->getCloseTag();
                if (!empty($closeVessel->getSubCommand())) {
                    // サブコマンド指定があった場合は、現在のサブコマンドを更新
                    $this->currentSubCommand = new CloserInfo($closeVessel->getTagClose(), $closeVessel->getSubCommand());
                }
                continue;
            }
            // 空行
            if ($vessel->getCommand() == Parser::SYSTEM_EMPTY_LINE) {
                $this->documentWork[] = PHP_EOL;
                continue;
            }
            // 通常
            // - スクリプト対応
            $scriptCheck = $vessel->getScript();
            if (!empty($scriptCheck['body'])) {
                $this->documentWork = array_merge($this->documentWork, $scriptCheck['body']);
            }
            // - タグの内容
            switch ($vessel->getBlockType) {
                case BaseTag::BLOCK_TYPE_INLINE:
                    $this->documentWork[] = $indentText
                                    . '<' . $vessel->getTagOpen()
                                    . ' ' . $vessel->getVerifiedAttributes() . '>'
                                    . $vessel->getText()
                                    . $vessel->getTagClose()
                                    . $comment;
                    break;
                case BaseTag::BLOCK_TYPE_BLOCK:
                    $this->documentWork[] = $indentText
                                          . '<' . $vessel->getTagOpen()
                                          . ' ' . $vessel->getVerifiedAttributes() . '>'
                                          . $comment;
                    break;
                case BaseTag::BLOCK_TYPE_BATCH:
                    $this->documentWork = array_merge($this->documentWork, $vessel->getBatch());
                    break;
                case BaseTag::BLOCK_TYPE_NONE:
                default:
                    $this->documentWork[] = $indentText . $vessel->getText();
                    break;
            }
        }

        // BODY用スクリプト
        if (!empty($this->scriptStack['BODY'])) {
            $this->documentWork[] = '<script>';
            foreach ($this->scriptStack['BODY'] as $line) {
                $this->documentWork[] = $line . PHP_EOL;
            }
            $this->documentWork[] = '</script>';
        }

        $this->documentWork[] = '    </body>';
        $this->documentWork[] = '</html>';
    }

    // For Debug
    public function getCloserStack(): array
    {
        return $this->closerStack;
    }

    // For Debug
    public function getScriptStack(): array
    {
        return $this->scriptStack;
    }

    // For Debug
    public function getCssStack(): array
    {
        return $this->cssStack;
    }

    // For Debug
    public function getVesselContainer(): array
    {
        return $this->vesselContainer;
    }

    // For Debug
    public function getDocumentWork(): array
    {
        return $this->documentWork;
    }

    // For Debug
    public function physicalTest(): string
    {
        return 'DONE';
    }
}
