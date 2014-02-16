<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;

class TextHelper extends Helper
{
    public function getName()
    {
        return 'text';
    }

    public function truncate($string, $length, $alignment = null, $delimString = null)
    {
        $alignment = $alignment === null ? 'left' : $alignment;
        $delimString = $delimString === null ? '...' : $delimString;
        $delimLen = strlen($delimString);

        if (!in_array($alignment, array('left', 'right'))) {
            throw new \InvalidArgumentException(
                'Alignment must either be "left" or "right"'
            );
        }

        if ($delimLen > $length) {
            throw new \InvalidArgumentException(sprintf(
                'Delimiter length "%s" cannot be greater than truncate length "%s"',
                $delimLen, $length
            ));
        }

        if (strlen($string) > $length) {
            $offset = $length - $delimLen;
            if ('left' === $alignment) {
                $string = substr($string, 0, $offset) . $delimString;
            } else {
                $string = $delimString . substr($string, 
                    strlen($string) - $offset
                );
            }
        }

        return $string;
    }
}
