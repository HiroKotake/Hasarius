<?php
/**
 * div.php
 * @var [type]
 */
namespace Hasarius\commands;

use Hasarius\system as System;

/**
 * divタグクラス
 */
class CommandDiv extends System\Command
{

    const TAG_OPEN        = "div";
    const TAG_CLOSE       = "</div>";
    const BLOCK_TYPE      = self::BLOCK_TYPE_SPARATE;
    const COMMAND_PERPOSE = [self::COMMAND_TYPE_HTML, self::COMMAND_TYPE_CSS, self::COMMAND_TYPE_SCRIPT];
    const COMMAND_ALIAS   = "div";

    public function __construct()
    {
        parent::setTagOpen(self::TAG_OPEN);
        parent::setTagClose(self::TAG_CLOSE);
        parent::setBlockType(self::BLOCK_TYPE);
        parent::setCommandPerpose(self::COMMAND_PERPOSE);
        parent::setCommandAlias(self::COMMAND_ALIAS);
    }
}
