<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\Shell\Query\UpdateParser;
use Jackalope\Query\QOM\ComparisonConstraint;
use Jackalope\Query\QOM\PropertyValue;
use PHPCR\Query\QOM\QueryObjectModelConstantsInterface;
use PHPCR\Query\QOM\LiteralInterface;

class QueryUpdateCommand extends Command
{
    /**
     * @var OutputInterface
     */
    protected $output;

    protected function configure()
    {
        $this->setName('update');
        $this->setDescription('Execute an UPDATE JCR-SQL2 query');
        $this->addArgument('query');
        $this->setHelp(<<<EOT
Execute a JCR-SQL2 update query. Unlike other commands you can enter a query literally:

     UPDATE [nt:unstructured] AS a SET title = 'foobar' WHERE a.title = 'barfoo';

You can update multivalue properties too:
    
     # Add a property
     UPDATE [nt:unstructured] AS a SET a.tags[] = 'foo'

     # Set values
     UPDATE [nt:unstructured] AS a SET a.tags = ['one', 'two', 'three', 'four']

     # Delete index
     UPDATE [nt:unstructured] SET a.tags[0] = NULL

     # Update a multivalue index by value
     UPDATE [nt:unstructured] SET a.tags = 'Foo' WHERE a.tags = 'Bar';

You must call <info>session:save</info> to persist changes.

Note that this command is not part of the JCR-SQL2 language but is implemented specifically
for the PHPCR-Shell.
EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $sql = $input->getRawCommand();

        // trim ";" for people used to MysQL
        if (substr($sql, -1) == ';') {
            $sql = substr($sql, 0, -1);
        }

        $session = $this->getHelper('phpcr')->getSession();
        $qm = $session->getWorkspace()->getQueryManager();

        $updateParser = new UpdateParser($qm->getQOMFactory());
        $res = $updateParser->parse($sql);
        $query = $res->offsetGet(0);
        $updates = $res->offsetGet(1);
        $constraint = $res->offsetGet(2);

        $start = microtime(true);
        $result = $query->execute();
        $rows = 0;

        foreach ($result as $row) {
            $rows++;
            foreach ($updates as $property) {
                $node = $row->getNode($property['selector']);

                if ($node->hasProperty($property['name'])) {
                    $value = $this->handleMultiValue($node, $property, $constraint);
                } else {
                    $value = $property['value'];
                }

                $node->setProperty($property['name'], $value);
            }
        }

        $elapsed = microtime(true) - $start;

        $output->writeln(sprintf('%s row(s) affected in %ss', $rows, number_format($elapsed, 2)));
    }

    protected function handleMultiValue($node, $propertyData, $constraint)
    {
        $phpcrProperty = $node->getProperty($propertyData['name']);
        $value = $propertyData['value'];

        if (false === $phpcrProperty->isMultiple()) {
            return $value;
        }

        $currentValue = $phpcrProperty->getValue();

        // there is an array operator ([] or [x])
        if (isset($propertyData['array_op'])) {
            $arrayOp = $propertyData['array_op'];
            if ($arrayOp === UpdateParser::ARRAY_OPERATION_ADD) {
                foreach ((array) $value as $v) {
                    $currentValue[] = $v;
                }

                return $currentValue;
            } elseif ($arrayOp === UpdateParser::ARRAY_OPERATION_SUB) {
                $arrayIndex = $propertyData['array_index'];

                if (!isset($currentValue[$arrayIndex])) {
                    throw new \InvalidArgumentException(sprintf(
                        'Multivalue index "%s" does not exist in multivalue field "%s"', $arrayIndex, $propertyData['name']
                    ));
                }

                if (null === $value) {
                    unset($currentValue[$arrayIndex]);
                    return array_values($currentValue);
                }

                if (is_array($value)) {
                    throw new \InvalidArgumentException(sprintf('Cannot set index to array value on "%s"', $propertyData['name']));
                }

                $currentValue[$arrayIndex] = $value;

                return $currentValue;
            }
        }

        if (is_array($value)) {
            return $value;
        }

        if ($constraint instanceof ComparisonConstraint) {

            $op1 = $constraint->getOperand1();
            $op2 = $constraint->getOperand2();
            $operator = $constraint->getOperator();
            if ($op1 instanceOf PropertyValue) {
                if (
                    ($propertyData['selector'] === null ||
                    $op1->getSelectorName() == $propertyData['selector']) &&
                    $op1->getPropertyName() == $propertyData['name']
                ) 
                {
                    if ($operator !== QueryObjectModelConstantsInterface::JCR_OPERATOR_EQUAL_TO) {
                        throw new \InvalidArgumentException(sprintf(
                            'You must use the "=" operator in the comparison when specifying a specific multiple value update'
                        ));
                    }

                    if (!$op2 instanceof LiteralInterface) {
                        throw new \InvalidArgumentException(sprintf(
                            'The value of "%s" must be a literal', $propertyData['name']
                        ));
                    }

                    $targetValue = $op2->getLiteralValue();

                    foreach ($currentValue as $k => $v) {
                        if ($v === $targetValue) {
                            if (null === $value) {
                                unset($currentValue[$k]);
                                $currentValue = array_values($currentValue);
                            } else {
                                $currentValue[$k] = $value;
                            }
                        }
                    }

                    return $currentValue;
                }
            }
        }

        // do not allow updating multivalue with scalar
        if (false === is_array($value) && sizeof($currentValue) > 1) {
            throw new \InvalidArgumentException(sprintf(
                '<error>Cannot update multivalue property "%s" with a scalar value.</error>',
                $phpcrProperty->getName()
            ));
        }

        return $value;
    }
}
