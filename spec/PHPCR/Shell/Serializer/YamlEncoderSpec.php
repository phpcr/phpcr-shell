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

class YamlEncoderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Serializer\YamlEncoder');
    }

    public function it_should_encode_to_yaml()
    {
        $data = array('foobar' => 'barfoo', 'barfoo' => 'foobar');
        $this->encode($data, 'yaml')->shouldReturn(<<<EOT
foobar: barfoo
barfoo: foobar

EOT
    );
    }

    public function is_should_decode_yaml()
    {
        $yaml = <<<EOT
foobar: barfoo
barfoo: foobar
EOT
        ;

        $this->decode($yaml, 'yaml')->shouldReturn(array(
            'foobar' => 'barfoo',
            'barfoo' => 'foobar'
        ));

    }
}
