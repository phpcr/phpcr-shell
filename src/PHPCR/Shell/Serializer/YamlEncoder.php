<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Serializer;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

/**
 * Encodes YAML data
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class YamlEncoder implements EncoderInterface, DecoderInterface
{
    /**
     * Encodes PHP data to a YAML string
     *
     * {@inheritdoc}
     */
    public function encode($data, $format, array $context = array())
    {
        return Yaml::dump($data);
    }

    public function decode($data, $format, array $context = array())
    {
        $arr = Yaml::parse($data);

        return $arr;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format)
    {
        return 'yaml' === $format;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDecoding($format)
    {
        return 'yaml' === $format;
    }
}
