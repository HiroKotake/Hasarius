<?php
namespace Hasarius\test;

require('../autoloader.php');
use Hasarius\AutoLoader;

$autoloader = new Autoloader();
$autoloader->autoload();

// (ダミー) 作成するDocument Type を設定
define('CURRENT_DOCUMENT_DATA', 'HTML4_LOOSE');
