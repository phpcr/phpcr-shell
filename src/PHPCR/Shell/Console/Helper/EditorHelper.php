<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * An file extension can be provided which will be appended
     * to the name of the temporary file, providing a type hint
     * to the editor.
     *
     * @param string $string
     * @param string $extension
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

        if ($extension) {
            $tmpName .= '.' . $extension;
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
