<?php

namespace PHPCR\Shell\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use PHPCR\NodeInterface;
use PHPCR\PropertyType;

class NodeNormalizer implements NormalizerInterface
{
    /**
     * {@inheritDoc}
     */
    public function normalize($node, $format = null, array $context = array())
    {
        $res = array();

        foreach ($node->getProperties() as $property) {
            $propertyType = $property->getType();
            $propertyValue = $property->getValue();
            $propertyName = $property->getName();

            $res[$propertyName] = array(
                'type' => PropertyType::nameFromValue($propertyType),
                'value' => $propertyValue
            );
        }

        return $res;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) && $data instanceof NodeInterface;
    }
}
