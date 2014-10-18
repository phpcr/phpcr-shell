<?php

namespace spec\PHPCR\Shell\Query;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PHPCR\Query\QOM\QueryObjectModelFactoryInterface;
use PHPCR\Query\QOM\JoinInterface;
use PHPCR\Query\QOM\SourceInterface;
use PHPCR\Query\QOM\ChildNodeJoinConditionInterface;
use PHPCR\Query\QOM\QueryObjectModelConstantsInterface;
use PHPCR\Query\QOM\PropertyValueInterface;
use PHPCR\Query\QOM\LiteralInterface;
use PHPCR\Query\QOM\ComparisonInterface;
use PHPCR\Query\QueryInterface;
use PHPCR\Shell\Query\FunctionOperand;
use PHPCR\Shell\Query\ColumnOperand;

class UpdateParserSpec extends ObjectBehavior
{
    function let(
        QueryObjectModelFactoryInterface $qomf
    )
    {
        $this->beConstructedWith(
            $qomf
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Query\UpdateParser');
    }

    function it_should_provide_a_qom_object_for_selecting(
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

    function it_should_parse_functions (
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
}
