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

namespace PHPCR\Shell\Query;

use PHPCR\Query\InvalidQueryException;
use PHPCR\Query\QOM\ConstraintInterface;
use PHPCR\Query\QOM\QueryObjectModelInterface;
use PHPCR\Query\QOM\SourceInterface;
use PHPCR\Util\QOM\Sql2Scanner;
use PHPCR\Util\QOM\Sql2ToQomQueryConverter;

/**
 * Parse "UPDATE" queries.
 *
 * This class extends the Sql2ToQomQueryConverter class and adapts it
 * to parse UPDATE queries.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class UpdateParser extends Sql2ToQomQueryConverter
{
    /**
     * Parse an "SQL2" UPDATE statement and construct a query builder
     * for selecting the rows and build a field => value mapping for the
     * update.
     *
     * @param string $sql2
     *
     * @return \ArrayObject{1: QueryObjectModelInterface, 2: array, 3: ?ConstraintInterface, 4: array}
     */
    public function parseUpdate($sql2): \ArrayObject
    {
        $this->scanner = new Sql2Scanner($sql2);
        $this->sql2 = $sql2;

        $this->implicitSelectorName = null;
        $source = null;
        $constraint = null;
        $updates = [];
        $applies = [];

        while ($this->scanner->lookupNextToken() !== '') {
            switch (strtoupper($this->scanner->lookupNextToken())) {
                case 'UPDATE':
                    $this->scanner->expectToken('UPDATE');
                    $source = $this->parseSource();
                    break;
                case 'SET':
                    $this->scanner->expectToken('SET');
                    $updates = $this->parseUpdates();
                    break;
                case 'APPLY':
                    $this->scanner->expectToken('APPLY');
                    $applies = $this->parseApply();
                    break;
                case 'WHERE':
                    $this->scanner->expectToken('WHERE');
                    $constraint = $this->parseConstraint();
                    break;
                default:
                    throw new InvalidQueryException('Expected end of query, got "'.$this->scanner->lookupNextToken().'" in '.$this->sql2);
            }
        }

        if (!$source instanceof SourceInterface) {
            throw new InvalidQueryException('Invalid query, source could not be determined: '.$sql2);
        }

        $query = $this->factory->createQuery($source, $constraint);

        return new \ArrayObject([$query, $updates, $constraint, $applies]);
    }

    /**
     * Parse the SET section of the query, returning
     * an array containing the property names (<selectorName.propertyName)
     * as keys and an array.
     *
     * @return array{'selector': string, 'name': string, 'value': mixed}
     */
    private function parseUpdates(): array
    {
        $updates = [];

        while (true) {
            $property = [
                'selector' => null,
                'name'     => null,
                'value'    => null,
            ];

            // parse left side
            $selectorName = $this->scanner->fetchNextToken();
            $delimiter = $this->scanner->fetchNextToken();

            if ($delimiter !== '.') {
                $property['selector'] = null;
                $property['name'] = $selectorName;
                $next = $delimiter;
            } else {
                $property['selector'] = $selectorName;
                $property['name'] = $this->scanner->fetchNextToken();
                $next = $this->scanner->fetchNextToken();
            }

            // parse right side
            $property['value'] = $this->parseOperand();

            $updates[] = $property;

            $next = $this->scanner->lookupNextToken();

            if (',' === $next) {
                $next = $this->scanner->fetchNextToken();
            } elseif (strtolower($next) === 'where' || !$next) {
                break;
            }
        }

        return $updates;
    }

    private function isLiteral($token)
    {
        if (str_starts_with($token, '\'')) {
            return true;
        }
        if (is_numeric($token)) {
            return true;
        }
        if (str_starts_with($token, '"')) {
            return true;
        }

        return false;
    }

    private function parseOperand()
    {
        $token = strtoupper($this->scanner->lookupNextToken());

        if ($this->scanner->lookupNextToken(1) === '(') {
            $functionData = $this->parseFunction();

            return new FunctionOperand($functionData[0], $functionData[1]);
        }

        if ($this->isLiteral($token)) {
            return $this->parseLiteralValue();
        }

        if ($token === 'NULL') {
            $this->scanner->fetchNextToken();

            return;
        }

        $columnData = $this->scanColumn();

        return new ColumnOperand($columnData[0], $columnData[1]);
    }

    private function parseApply()
    {
        $functions = [];

        while (true) {
            $token = strtoupper($this->scanner->lookupNextToken());

            if ($this->scanner->lookupNextToken(1) === '(') {
                $functionData = $this->parseFunction();

                $functions[] = new FunctionOperand($functionData[0], $functionData[1]);
            }

            $next = $this->scanner->lookupNextToken();

            if (',' === $next) {
                $next = $this->scanner->fetchNextToken();
            } elseif (strtolower($next) === 'where' || !$next) {
                break;
            }
        }

        return $functions;
    }

    private function parseFunction()
    {
        $functionName = $this->scanner->fetchNextToken();
        $this->scanner->expectToken('(');

        $args = [];
        $next = true;
        while ($next && $next !== ')') {
            $args[] = $this->parseOperand();

            $next = $this->scanner->fetchNextToken();
            if (!in_array($next, [',', ')', ''])) {
                throw new InvalidQueryException(sprintf('Invalid function argument delimiter "%s" in "%s"', $next, $this->sql2));
            }
        }

        return [$functionName, $args];
    }
}
