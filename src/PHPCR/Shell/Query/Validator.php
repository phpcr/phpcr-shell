<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace \PHPCR\Shell\Query;

class Validator
{
    /**
     * Assert that queries are terminated with ";"
     *
     * @param string $sql2
     */
    public static function validateQuery($sql2)
    {
        if (substr($sql2, -1) !== ';') {
            throw new \InvalidArgumentException(
                'Queries must be terminated with ";"'
            );
        }
    }
}
