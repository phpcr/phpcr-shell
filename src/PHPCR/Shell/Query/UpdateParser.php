<?php

namespace PHPCR\Shell\Query;

use PHPCR\Util\ValueConverter;
use PHPCR\Util\QOM\Sql2Scanner;
use PHPCR\Util\QOM\Sql2ToQomQueryConverter;
use PHPCR\Query\InvalidQueryException;
use PHPCR\Query\QOM\SourceInterface;

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
     * @return array($query, $updates)
     */
    public function parse($sql2)
    {
        $this->implicitSelectorName = null;
        $this->sql2 = $sql2;
        $this->scanner = new Sql2Scanner($sql2);
        $source = null;
        $constraint = null;

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
                case 'WHERE':
                    $this->scanner->expectToken('WHERE');
                    $constraint = $this->parseConstraint();
                    break;
                default:
                    throw new InvalidQueryException('Expected end of query, got "' . $this->scanner->lookupNextToken() . '" in ' . $this->sql2);
            }
        }

        if (!$source instanceof SourceInterface) {
            throw new InvalidQueryException('Invalid query, source could not be determined: '.$sql2);
        }

        $query = $this->factory->createQuery($source, $constraint);

        $res = new \ArrayObject(array($query, $updates, $constraint));

        return $res;
    }

    /**
     * Parse the SET section of the query, returning
     * an array containing the property names (<selectorName.propertyName)
     * as keys and an array
     * 
     * array(
     *     'selector' => <selector>,
     *     'name' => <name>,
     *     '<value>' => <property value>,
     * )
     *
     * @return array
     */
    protected function parseUpdates()
    {
        $updates = array();

        while (true) {
            $selectorName = $this->scanner->fetchNextToken();
            $delimiter = $this->scanner->fetchNextToken();

            if ($delimiter !== '.') {
                $property = array(
                    'selector' => null,
                    'name' => $selectorName
                );
                $equals = $delimiter;
            } else {
                $property = array(
                    'selector' => $selectorName,
                    'name' => $this->scanner->fetchNextToken()
                );
                $equals = $this->scanner->fetchNextToken();
            }


            if ($equals !== '=') {
                throw new InvalidQueryException(sprintf(
                    'Expected "=" after property name in UPDATE query, got "%s"',
                    $equals,
                    $this->sql2
                ));
            }

            $value = $this->parseLiteralValue();
            $property['value'] = $value;

            $updates[$property['selector'] . '.' . $property['name']] = $property;

            $next = $this->scanner->lookupNextToken();

            if ($next == ',') {
                $next = $this->scanner->fetchNextToken();
            } elseif (strtolower($next) == 'where' || !$next) {
                break;
            }
        }

        return $updates;
    }
}
