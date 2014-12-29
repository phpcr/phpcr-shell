<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\PHPCR\Shell\Serializer;

use PhpSpec\ObjectBehavior;
use PHPCR\NodeInterface;
use PHPCR\PropertyInterface;
use PHPCR\PropertyType;

class NodeNormalizerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Serializer\NodeNormalizer');
    }

    public function it_can_normalize_a_node_to_an_array(
        NodeInterface $node,
        PropertyInterface $p1,
        PropertyInterface $p2,
        PropertyInterface $p3
    )
    {
        $node->getProperties()->willReturn(array(
            $p1, $p2, $p3
        ));

        $p1->getName()->willReturn('my:property.1');
        $p1->getType()->willReturn(PropertyType::STRING);
        $p1->getValue()->willReturn('P1 Val');
        $p2->getName()->willReturn('my:property.2');
        $p2->getType()->willReturn(PropertyType::DOUBLE);
        $p2->getValue()->willReturn('P2 Val');
        $p3->getName()->willReturn('my:property.3');
        $p3->getType()->willReturn(PropertyType::STRING);
        $p3->getValue()->willReturn('P3 Val');

        $this->normalize($node)->shouldReturn(array(
            'my:property.1' => array(
                'type' => 'String',
                'value' => 'P1 Val',
            ),
            'my:property.2' => array(
                'type' => 'Double',
                'value' => 'P2 Val',
            ),
            'my:property.3' => array(
                'type' => 'String',
                'value' => 'P3 Val',
            ),
        ));
    }
}
