<?php

namespace Hasarius;

require_one('./autoloader.php');
$autoload = new \Hasarius\AutoLoader();
$autoload->autoload();

$genarate = new Hasarius\system\Genarate();
$genarate->make(["source" => $source]);
