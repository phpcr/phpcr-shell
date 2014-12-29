<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Input;

use PHPCR\Shell\Console\Application\ShellApplication;
use PHPCR\Shell\Phpcr\PhpcrSession;

/**
 * Class for autocompleting commands
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class AutoComplete
{
    private $application;
    private $session;

    public function __construct(ShellApplication $application, PhpcrSession $session)
    {
        $this->application = $application;
        $this->session = $session;
    }

    public function autocomplete($text)
    {
        $list = array_keys($this->application->all());

        $node = $this->session->getCurrentNode();

        foreach ($node->getNodes() as $node) {
            $list[] = $node->getName();
        }

        foreach ($node->getProperties() as $property) {
            $list[] = $property->getName();
        }

        return $list;
    }
}
