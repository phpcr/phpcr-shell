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

namespace PHPCR\Shell\Console\Input;

use Symfony\Component\Console\Input\StringInput as BaseInput;

/**
 * Extend the Symfony StringInput class to provide additional accessors
 * and methods.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class StringInput extends BaseInput
{
    protected $rawCommand;
    protected $tokens;
    protected $isQuery = false;

    /**
     * {@inheritdoc}
     */
    public function __construct($command)
    {
        $this->rawCommand = trim($command);

        if (stripos($this->rawCommand, 'select') === 0) {
            $command = 'select'.substr($command, 6);
            $this->isQuery = true;
        }

        if (stripos($this->rawCommand, 'update') === 0) {
            $command = 'update'.substr($command, 6);
            $this->isQuery = true;
        }

        if (stripos($this->rawCommand, 'delete') === 0) {
            $command = 'delete'.substr($command, 6);
            $this->isQuery = true;
        }

        parent::__construct($command);
    }

    /**
     * Return the raw command string without any parsing.
     *
     * (useful for returning the full SQL query for example)
     *
     * @return string
     */
    public function getRawCommand()
    {
        return $this->rawCommand;
    }

    public function validate(): void
    {
        if (false === $this->isQuery()) {
            parent::validate();
        }
    }

    /**
     * Do not validate if the command is a query.
     *
     * {@inheritdoc}
     */
    protected function parse(): void
    {
        if (false === $this->isQuery()) {
            parent::parse();
        }
    }

    /**
     * Provide access to the tokens as this is not
     * allowed by the default StringInput and we require
     * it for the "alias" feature.
     */
    protected function setTokens(array $tokens): void
    {
        $this->tokens = $tokens;
        parent::setTokens($tokens);
    }

    /**
     * Return the tokens for this command (as recognized
     * by the parse() method).
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Return true if this command sounds like a query, i.e.
     * if it begins with "select ".
     *
     * @return bool
     */
    protected function isQuery()
    {
        return $this->isQuery;
    }
}
