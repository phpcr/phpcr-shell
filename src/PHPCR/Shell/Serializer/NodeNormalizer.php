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

namespace PHPCR\Shell\Serializer;

use PHPCR\NodeInterface;
use PHPCR\PropertyInterface;
use PHPCR\PropertyType;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizer for PHPCR Nodes.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class NodeNormalizer implements NormalizerInterface, DenormalizerInterface
{
    protected $allowBinary;
    protected $notes = [];

    public function __construct($allowBinary = false)
    {
        $this->allowBinary = $allowBinary;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($node, $format = null, array $context = []): ArrayObject|array|string|int|float|bool|null
    {
        $res = [];

        foreach ($node->getProperties() as $property) {
            if (false === $this->isPropertyEditable($property)) {
                continue;
            }

            $propertyType = $property->getType();

            $propertyValue = $property->getValue();
            $propertyName = $property->getName();

            if (in_array($property->getType(), [PropertyType::REFERENCE, PropertyType::WEAKREFERENCE])) {
                $nodeUuids = [];

                if (false === is_array($propertyValue)) {
                    $propertyValue = [$propertyValue];
                }

                foreach ($propertyValue as $node) {
                    $nodeUuids[] = $node->getIdentifier();
                }
                $propertyValue = $nodeUuids;

                if (false === $property->isMultiple()) {
                    $propertyValue = reset($propertyValue);
                }
            }

            $res[$propertyName] = [
                'type'  => PropertyType::nameFromValue($propertyType),
                'value' => $propertyValue,
            ];
        }

        return $res;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            NodeInterface::class => true,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return is_object($data) && $data instanceof NodeInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = []): mixed
    {
        if (!$data) {
            throw new \InvalidArgumentException(
                'Editor returned nothing .. nodes must have at least one property (i.e. the jcr:primaryType property)'
            );
        }

        if (!isset($context['node'])) {
            throw new \InvalidArgumentException(sprintf(
                'You must provide the PHPCR node instance to update in the context using the "node" key.'
            ));
        }

        $node = $context['node'];

        $errors = [];

        // Update / remove existing properties
        foreach ($node->getProperties() as $property) {
            if (false === $this->isPropertyEditable($property)) {
                continue;
            }

            try {
                if (!isset($data[$property->getName()])) {
                    $property->remove();
                    continue;
                }

                $datum = $this->normalizeDatum($data[$property->getName()]);
                $typeValue = isset($datum['type']) ? PropertyType::valueFromName($datum['type']) : null;

                if (isset($datum['value'])) {
                    // if the type or the value is differnet, update the property
                    if ($datum['value'] != $property->getValue() || $typeValue != $property->getType()) {
                        // setValue doesn't like being passed a null value as a type ...
                        if ($typeValue !== null) {
                            $property->setValue($datum['value'], $typeValue);
                        } else {
                            $property->setValue($datum['value']);
                        }
                    }
                }
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            unset($data[$property->getName()]);
        }

        // Add new properties
        foreach ($data as $pName => $datum) {
            $datum = $this->normalizeDatum($datum);
            $pValue = isset($datum['value']) ? $datum['value'] : null;
            $pType = isset($datum['type']) ? PropertyType::valueFromName($datum['type']) : null;

            if ($pValue !== null) {
                $node->setProperty($pName, $pValue, $pType);
            }
        }

        if (count($errors) > 0) {
            throw new InvalidArgumentException(sprintf(
                'Errors encountered during denormalization: %s',
                implode("\n", $errors)
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return $type === 'PHPCR\NodeInterface';
    }

    /**
     * If the value is a scalar value convert it into
     * an array with default values.
     *
     * @param mixed
     *
     * @return string
     */
    private function normalizeDatum($value)
    {
        if (is_scalar($value)) {
            return [
                'value' => $value,
                'type'  => null,
            ];
        }

        return $value;
    }

    /**
     * Return false if property type is not editable.
     *
     * (e.g. property type is binary)
     *
     * @return bool
     */
    private function isPropertyEditable(PropertyInterface $property)
    {
        // do not serialize binary objects
        if (false === $this->allowBinary && PropertyType::BINARY == $property->getType()) {
            $this->notes[] = sprintf(
                'Binary property "%s" has been omitted',
                $property->getName()
            );

            return false;
        }

        return true;
    }
}
