<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\Helper;

/**
 * Helper for launching external editor
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class EditorHelper extends Helper
{
    /**
     * Launch an external editor and open a temporary
     * file containing the given string value.
     *
     * @param string $string
     * @param string $extension Optional extension for filetype hinting (e.g. .yml)
     *
     * @return string
     */
    public function fromString($string, $extension = null)
    {
        $fs = new Filesystem();
        $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'phpcr-shell';

        if (!file_exists($dir)) {
            $fs->mkdir($dir);
        }

        $tmpName = tempnam($dir, '');

        if (null !== $extension) {
            $tmpName .= $extension;
        }

        file_put_contents($tmpName, $string);
        $editor = getenv('EDITOR');

        if (!$editor) {
            throw new \RuntimeException('No EDITOR environment variable set.');
        }

        system($editor . ' ' . $tmpName . ' > `tty`');

        $contents = file_get_contents($tmpName);
        $fs->remove($tmpName);

        return $contents;
    }

    public function fromStringWithMessage($string, $message, $messagePrefix = '# ', $extension = null)
    {
        if (null !== $message) {
            $message = explode("\n", $message);

            foreach ($message as $line) {
                $source[] = $messagePrefix.$line;
            }
            $source = implode("\n", $source).PHP_EOL;
        } else {
            $source = '';
        }

        $source .= $string;

        $res = $this->fromString($source, $extension);
        $res = explode("\n", $res);

        $line = current($res);

        while (0 === strpos($line, $messagePrefix)) {
            $line = next($res);
        }

        $out = array();

        while ($line) {
            $out[] = $line;
            $line = next($res);
        }

        return implode("\n", $out);
    }

    public function getName()
    {
        return 'editor';
    }
}
