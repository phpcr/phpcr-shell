<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;

/**
 * Helper for text plain text formatting.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class TextHelper extends Helper
{
    /**
     * @todo: Make this configurable
     *
     * @var int
     */
    protected $truncateLength = 75;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'text';
    }

    /**
     * Truncate the given string.
     *
     * @param string $string      String to truncate
     * @param int    $length      Truncate to this length
     * @param string $alignment   Align to the "left" or the "right"
     * @param string $delimString String to use to use to indicate the truncation
     */
    public function truncate($string, $length = null, $alignment = null, $delimString = null): string
    {
        if (null === $length) {
            $length = $this->truncateLength;
        }

        $alignment = $alignment ?? 'left';
        $delimString = $delimString ?? '...';
        $delimLen = strlen($delimString);

        if (!in_array($alignment, ['left', 'right'])) {
            throw new \InvalidArgumentException(
                'Alignment must either be "left" or "right"'
            );
        }

        if ($delimLen > $length) {
            throw new \InvalidArgumentException(sprintf(
                'Delimiter length "%s" cannot be greater than truncate length "%s"',
                $delimLen,
                $length
            ));
        }

        if (strlen($string) > $length) {
            $offset = $length - $delimLen;
            if ('left' === $alignment) {
                $string = substr($string, 0, $offset).$delimString;
            } else {
                $string = $delimString.substr(
                    $string,
                    strlen($string) - $offset
                );
            }
        }

        return $string;
    }
}
