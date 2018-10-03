<?php
namespace Hasarius;

/**
 * Hasarius AutoLoader
 */
class AutoLoader
{
    public function autoload(): void
    {
        spl_autoload_register(array($this,'hasariusAutoload'));
    }

    private function hasariusAutoload(string $class): void
    {
        // 全社共通
        if (preg_match('/^jp\\\teleios\\\libs\\\.*$/u', $class) != 0) {
            $dirsAndFileHead = explode('\\', $class);
            $filename = array_pop($dirsAndFileHead);
            $filename = __DIR__ . DIRECTORY_SEPARATOR
                      . 'libs' . DIRECTORY_SEPARATOR
                      . strtolower($filename) . '.php';
            require_once $filename;
            return;
        }

        // プロジェクト共通
        if (!preg_match('/^Hasarius/', $class)) {
            return;
        }
        $dirsAndFileHead = explode('\\', $class);
        $filename = array_pop($dirsAndFileHead);
        $headFilename = strtolower($filename);
        $subDir = array_pop($dirsAndFileHead);
        $preprocessPattern = '/^Preprocess.*$/';
        $commandPattern    = '/^Command.*$/';
        $decorationPattern = '/^Decorate.*$/';
        $utilsPattern      = '/^utils$/';

        // for system
        $filename = __DIR__ . DIRECTORY_SEPARATOR
                  . 'system' . DIRECTORY_SEPARATOR
                  . strtolower($filename) . '.php';
        if (preg_match($preprocessPattern, $filename)) {
            // for preprocess
            $filename = strtolower(preg_replace('/^Preprocess/', '', $filename));
            $filename = __DIR__ . DIRECTORY_SEPARATOR
                      . 'preprocess' . DIRECTORY_SEPARATOR
                      . $headFilename . DIRECTORY_SEPARATOR
                      . $headFilename . '.php';
        } elseif (preg_match($commandPattern, $filename)) {
            // for command
            $filename = strtolower(preg_replace('/^Command/', '', $filename));
            $filename = __DIR__ . DIRECTORY_SEPARATOR
                      . 'commands' . DIRECTORY_SEPARATOR
                      . $headFilename . DIRECTORY_SEPARATOR
                      . $headFilename . '.php';
        } elseif (preg_match($decorationPattern, $filename)) {
            // for decoration
            $filename = strtolower(preg_replace('/^Decorate/', '', $filename));
            $filename = __DIR__ . DIRECTORY_SEPARATOR
                      . 'decorations' . DIRECTORY_SEPARATOR
                      . $headFilename . DIRECTORY_SEPARATOR
                      . $headFilename . '.php';
        } elseif (preg_match($utilsPattern, $subDir)) {
            // for utils
            $filename = __DIR__ . DIRECTORY_SEPARATOR
                      . 'utils' . DIRECTORY_SEPARATOR
                      . $headFilename . '.php';
        }

        if (!file_exists($filename)) {
            throw new \Exception('[ERROR] Autoloader - Class File Not Found !! (' . $class . ')');
        }
        require_once $filename;
    }
}
