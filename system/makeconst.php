<?php
/**
 * makeconst.php
 *
 * @package Hasarius
 * @category system
 * @author Takahiro Kotake
 * @license Teleios Development
 */

namespace Hasarius\system;

class MakeConst
{

    public static function load(string $file = null): void
    {
        $file = $file ?? __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'const.json';
        $json = file_get_contents($file);
        $constBases = json_decode($json, true);
        foreach ($constBases as $key => $info) {
            if (!defined($key)) {
                define($key, $info);
            }
        }
    }

    public static function loadMakeFile(string $file): void
    {
        if (!file_exists($file)) {
            throw new Exception("[ERROR] make.json file is not existed !!", 1);
        }

        $json = file_get_contents($file);
        $constBases = json_decode($json, true);
        foreach ($constBases as $key => $info) {
            $makeKey = "MAKE_" . $key;
            if (!defined($makeKey)) {
                define($makeKey, $info);
            }
        }
    }

    public static function getDocumentType(): string
    {
        $dtd = "";
        switch (MAKE_DocumentType) {
            case "HTML4_LOOSE":
                $dtd = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
                break;
            case "HTML4_STRICT":
                $dtd = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\">";
                break;
            case "HTML4_FRAME":
                $dtd = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\">";
                break;
            case "XHTML1_LOOSE":
                $dtd = "<?xml version=\"1.0\" encoding=\"" . MAKE_Charset . "\"?>" . PHP_EOL
                     . "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
                break;
            case "XHTML1_STRICT":
                $dtd = "<?xml version=\"1.0\" encoding=\"" . MAKE_Charset . "\"?>" . PHP_EOL
                     . "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">";
                break;
            case "XHTML1_FRAME":
                $dtd = "<?xml version=\"1.0\" encoding=\"" . MAKE_Charset . "\"?>" . PHP_EOL
                     . "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">";
                break;
            case "XHTML1_1":
                $dtd = "<?xml version=\"1.0\" encoding=\"" . MAKE_Charset . "\"?>" . PHP_EOL
                     . "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">";
                break;
            case "HTML5":
            case "HTML5_1":
            default:
                $dtd = "<!DOCTYPE html>";
                break;
        }
        return $dtd;
    }

    public static function isDefinedCharset(): bool
    {
        if (defined("MAKE_DocumentType") && preg_match("/^HTML5.*$/", MAKE_DocumentType) != 0 && defined('MAKE_Charset')) {
            return true;
        }
        return false;
    }

    public static function makeMetaParts(): array
    {
        $metaList = [];
        // Charset
        if (self::isDefinedCharset()) {
            $metaList[] = "<meta charset=\"" . MAKE_Charset . "\">";
        }
        // etc
        foreach (MAKE_Meta as $key => $value) {
            if (!array_key_exists($key, HEAD_META)) {
                throw new \Exception("[ERROR:" . __METHOD__ . "] $key can not use !!");
            }
            $info = HEAD_META[$key];
            if ($key == "Property") {
                if (defined("MAKE_DocumentType") && preg_match("/^HTML5.*$/", MAKE_DocumentType) != 0) {
                    foreach ($value as $subKey => $subValue) {
                        $metaList[] = "<meta " . $info["attribute"] . "=\"$subKey\" content=\"$subValue\">";
                    }
                }
                continue;
            }
            // エンコード関連
            if ($key == "ContentType" && self::isDefinedCharset()) {
                continue;
            }
            $metaList[] = "<meta " . $info["attribute"] . "=\"" . $info["origin"] . "\" content=\"$value\">";
        }
        return $metaList;
    }

    public static function makeScriptParts(): array
    {
        $scriptList = [];
        foreach (MAKE_Script as $part) {
            $line = "<script";
            foreach ($part as $attrib => $value) {
                $line .= " " . $attrib . "=\"$value\"";
            }
            $line .= "></script>";
            $scriptList[] = $line;
        }
        return $scriptList;
    }

    public static function makeLinkParts(): array
    {
        $scriptList = [];
        foreach (MAKE_Link as $part) {
            $line = "<link";
            foreach ($part as $attrib => $value) {
                $line .= " " . $attrib . "=\"$value\"";
            }
            $line .= ">";
            $scriptList[] = $line;
        }
        return $scriptList;
    }

    public static function getTagHtml(): string
    {
        $tagHtml = "<html";
        if (preg_match("/^HTML5\S*$/ui", MAKE_DocumentType) != 0) {
            if (defined("MAKE_HtmlClass") && !empty(MAKE_HtmlClass)) {
                $tagHtml .= ' class="';
                $classWork = "";
                foreach (MAKE_HtmlClass as $cssName) {
                    $classWork .= $cssName . " ";
                }
                $tagHtml .= rtrim($classWork) . '"';
            }
            if (!defined('MAKE_BasePosition') || MAKE_BasePosition == "html") {
                if (defined('MAKE_Prefix') && !empty('MAKE_Prefix')) {
                    $prefixStr = "";
                    foreach (MAKE_Prefix as $prefix) {
                        $prefixStr .= $prefix . " ";
                    }
                    $tagHtml .= ' prefix="' . rtrim($prefixStr) . '"';
                    $tagHtml .= ' lang="' . MAKE_Language . '"';
                }
            }
        } elseif (preg_match("/^XHTML\S*$/ui", MAKE_DocumentType) != 0) {
            $tagHtml .= " xmlns=\"http://www.w3.org/1999/xhtml\"";
        }
        $tagHtml .= ">";
        return $tagHtml;
    }

    public static function getTagHead(): string
    {
        $tagHtml = "<head";
        if (defined('MAKE_BasePosition') && MAKE_BasePosition == "head") {
            if (preg_match("/^HTML5\S*$/ui", MAKE_DocumentType) != 0 && defined('MAKE_Prefix') && !empty('MAKE_Prefix')) {
                $prefixStr = "";
                foreach (MAKE_Prefix as $prefix) {
                    $prefixStr .= $prefix;
                }
                $tagHtml .= ' prefix="' . $prefixStr . '"';
            }
            $tagHtml .= ' lang="' . MAKE_Language . '"';
        }
        $tagHtml .= ">";
        return $tagHtml;
    }
}
