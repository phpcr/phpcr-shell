<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\PHPCR\Shell\Query;

use PhpSpec\ObjectBehavior;
use PHPCR\Query\QOM\QueryObjectModelFactoryInterface;
use PHPCR\Query\QOM\JoinInterface;
use PHPCR\Query\QOM\SourceInterface;
use PHPCR\Query\QOM\ChildNodeJoinConditionInterface;
use PHPCR\Query\QOM\QueryObjectModelConstantsInterface;
use PHPCR\Query\QOM\PropertyValueInterface;
use PHPCR\Query\QOM\LiteralInterface;
use PHPCR\Query\QOM\ComparisonInterface;
use PHPCR\Query\QueryInterface;

class UpdateParserSpec extends ObjectBehavior
{
    public function let(
        QueryObjectModelFactoryInterface $qomf
    )
    {
        $this->beConstructedWith(
            $qomf
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Query\UpdateParser');
    }

    public function it_should_provide_a_qom_object_for_selecting(
        QueryObjectModelFactoryInterface $qomf,
        ChildNodeJoinConditionInterface $joinCondition,
        JoinInterface $join,
        SourceInterface $parentSource,
        SourceInterface $childSource,
        PropertyValueInterface $childValue,
        LiteralInterface $literalValue,
        ComparisonInterface $comparison,
        QueryInterface $query
    )
    {
        $qomf->selector('parent', 'mgnl:page')->willReturn($parentSource);
        $qomf->selector('child', 'mgnl:metaData')->willReturn($childSource);
        $qomf->childNodeJoinCondition('child', 'parent')->willReturn($joinCondition);
        $qomf->join($parentSource, $childSource, QueryObjectModelConstantsInterface::JCR_JOIN_TYPE_INNER, $joinCondition)->willReturn($join);
        $qomf->propertyValue('child', 'mgnl:template')->willReturn($childValue);
        $qomf->literal('standard-templating-kit:stkNews')->willReturn($literalValue);
        $qomf->comparison($childValue, QueryObjectModelConstantsInterface::JCR_OPERATOR_EQUAL_TO, $literalValue)->willReturn($comparison);

        $qomf->createQuery($join, $comparison)->willReturn($query);

        $sql = <<<EOT
UPDATE [mgnl:page] AS parent
    INNER JOIN [mgnl:metaData] AS child ON ISCHILDNODE(child,parent)
    SET
        parent.foo = 'PHPCR\\FOO\\Bar',
        parent.bar = 'foo'
    WHERE
        child.[mgnl:template] = 'standard-templating-kit:stkNews'
EOT;
        $res = $this->parse($sql);

        $res->offsetGet(0)->shouldHaveType('PHPCR\Query\QueryInterface');
        $res->offsetGet(1)->shouldReturn(array(
            array(
                'selector' => 'parent',
                'name' => 'foo',
                'value' => 'PHPCR\\FOO\\Bar',
            ),
            array(
                'selector' => 'parent',
                'name' => 'bar',
                'value' => 'foo',
            ),
        ));
    }

    public function it_should_parse_functions (
        QueryObjectModelFactoryInterface $qomf,
        SourceInterface $source,
        QueryInterface $query
    )
    {
        $qomf->selector('a', 'dtl:article')->willReturn($source);
        $qomf->createQuery($source, null)->willReturn($query);


        $sql = <<<EOT
UPDATE [dtl:article] AS a SET a.tags = array_replace(a.tags, 'asd', 'dsa')
EOT;
        $res = $this->parse($sql);

        $res->offsetGet(0)->shouldHaveType('PHPCR\Query\QueryInterface');
    }

    public function it_should_parse_apply (
        QueryObjectModelFactoryInterface $qomf,
        SourceInterface $source,
        QueryInterface $query
    )
    {
        $qomf->selector('a', 'dtl:article')->willReturn($source);
        $qomf->createQuery($source, null)->willReturn($query);


        $sql = <<<EOT
UPDATE [dtl:article] AS a APPLY nodetype_add('nt:barbar')
EOT;
        $res = $this->parse($sql);

        $res->offsetGet(0)->shouldHaveType('PHPCR\Query\QueryInterface');
    }
}
