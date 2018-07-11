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
        if (!preg_match('|^Hasarius|', $class)) {
            return;
        }
        $dirsAndFileHead = explode('\\', $class);
        $filename = array_pop($dirsAndFileHead);
        $subDir = array_pop($dirsAndFileHead);
        $commandPattern    = '|^Command.*$|';
        $decorationPattern = '|^Decorate.*$|';
        $utilsPattern      = '|^utils$|';

        // for system
        $filename = __DIR__ . DIRECTORY_SEPARATOR
                  . 'system' . DIRECTORY_SEPARATOR
                  . strtolower($filename) . '.php';
        if (preg_match($commandPattern, $filename)) {
            // for command
            $filename = strtolower(preg_replace('|^Command|', '', $filename));
            $filename = __DIR__ . DIRECTORY_SEPARATOR
                      . 'commands' . DIRECTORY_SEPARATOR
                      . $filename . DIRECTORY_SEPARATOR
                      . $filename . '.php';
        } elseif (preg_match($decorationPattern, $filename)) {
            // for decoration
            $filename = strtolower(preg_replace('|^Decorate|', '', $filename));
            $filename = __DIR__ . DIRECTORY_SEPARATOR
                      . 'decorations' . DIRECTORY_SEPARATOR
                      . $filename . DIRECTORY_SEPARATOR
                      . $filename . '.php';
        } elseif (preg_match($utilsPattern, $subDir)) {
            // for utils
            $filename = __DIR__ . DIRECTORY_SEPARATOR
                      . 'utils' . DIRECTORY_SEPARATOR
                      . strtolower($filename) . '.php';
        }

        if (!file_exists($filename)) {
            throw new \Exception('[ERROR] Class File Not Found !! (' . $class . ')');
        }
        require $filename;
    }
}
