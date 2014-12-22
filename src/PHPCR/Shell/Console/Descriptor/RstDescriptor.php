<?php

namespace PHPCR\Shell\Console\Descriptor;

use Symfony\Component\Console\Descriptor\Descriptor;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Descriptor\ApplicationDescription;

/**
 * Class which dumps the command reference in RST format
 * for use by the official documentation.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class RstDescriptor extends Descriptor
{
    protected $ignoreOptions = array(
        'verbose',
        'version',
        'quiet',
        'ansi',
        'no-ansi',
        'no-interaction',
    );

    protected function getCommandRefName($name)
    {
        return 'phpcr_shell_command_' . str_replace(':', '', $name);
    }

    protected function underline($string, $char)
    {
        return str_repeat($char, strlen($string));
    }

    protected function formatText($text)
    {
        $lines = explode("\n", $text);
        $newLines = array();
        $blockLines = array();

        foreach ($lines as $line) {
            // if line is indented by 2 or 4 spaces, assume
            // that it is a code block
            if (preg_match('{^[    |  ]}', $line)) {
                $inBlock = true;
                $blockLines = array();
            } else {
                $inBlock = false;
            }

            if (true === $inBlock) {
                $blockLines[] = $line;
                continue;
            }

            if (false === $inBlock && $blockLines) {
                $newLines[] = '';
                $newLines[] = '.. code-block:: bash';
                $newLines[] = '';
                foreach ($blockLines as $blockLine) {
                    $blockLine = preg_replace('{( +)<(.*?)>(.*)</(.*)>}', '\3', $blockLine);
                    $newLines[] = '    ' . $blockLine;
                }
                $newLines[] = '';
                $blockLines = array();
            } else {
                // replace inline tags with literals
                $line = preg_replace('{<(.*?)>(.*)</(.*)>}', '``\2``', $line);
                $newLines[] = $line;
            }
        }


        return implode("\n", $newLines);
    }

    /**
     * {@inheritdoc}
     */
    protected function describeInputArgument(InputArgument $argument, array $options = array())
    {
        return implode("\n", array(
            $argument->getName(),
            $this->underline($argument->getName(), '"'),
            '',
            '* **Name:** ``'. ($argument->getName() ?: '*<none>*').'``',
            '* **Is required:** '.($argument->isRequired() ? 'yes' : 'no'),
            '* **Is array:** '.($argument->isArray() ? 'yes' : 'no'),
            '* **Description:** '.($argument->getDescription() ?: '*<none>*'),
            '* **Default:** ``'.str_replace("\n", '', var_export($argument->getDefault(), true)).'``',
            '',
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function describeInputOption(InputOption $option, array $options = array())
    {
        if (in_array($option->getName(), $this->ignoreOptions)) {
            return '';
        }

        return implode("\n", array(
            $option->getName(),
            $this->underline($option->getName(), '"'),
            '',
            '* **Name:** ``--'.$option->getName().'``',
            '* **Accept value:** '.($option->acceptValue() ? 'yes' : 'no'),
            '* **Is value required:** '.($option->isValueRequired() ? 'yes' : 'no'),
            '* **Is multiple:** '.($option->isArray() ? 'yes' : 'no'),
            '* **Description:** '.($option->getDescription() ?: '*<none>*'),
            '* **Default:** ``'.str_replace("\n", '', var_export($option->getDefault(), true)).'``',
            '',
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function describeInputDefinition(InputDefinition $definition, array $options = array())
    {
        $blocks = array();

        if (count($definition->getArguments()) > 0) {
            $blocks[] = 'Arguments:';
            $blocks[] = '~~~~~~~~~~';
            $blocks[] = '';
            foreach ($definition->getArguments() as $argument) {
                $blocks[] = $this->describeInputArgument($argument);
            }
            $blocks[] = '';
        }

        if (count($definition->getOptions()) > 0) {
            $blocks[] = 'Options:';
            $blocks[] = '~~~~~~~~';
            $blocks[] = '';
            foreach ($definition->getOptions() as $option) {
                $blocks[] = $this->describeInputOption($option);
            }
            $blocks[] = '';
        }

        return implode("\n", $blocks);
    }

    /**
     * {@inheritdoc}
     */
    protected function describeCommand(Command $command, array $options = array())
    {
        $command->getSynopsis();
        $command->mergeApplicationDefinition(false);

        $rst = array(
            '',
            '.. _' . $this->getCommandRefName($command->getName()) . ':',
            '',
            $command->getName(),
            $this->underline($command->getName(), '-'),
            '',
            '* **Description:** '.($command->getDescription() ? $this->formatText($command->getDescription()) : '*<none>*'),
            '* **Usage:** ``'.$command->getSynopsis().'``',
        );

        $rst[] = '';

        if ($help = $command->getProcessedHelp()) {
            $rst[] = $this->formatText($help);
            $rst[] = '';
        }

        if ($definitionRst = $this->describeInputDefinition($command->getNativeDefinition())) {
            $rst[] = $this->formatText($definitionRst);
            $rst[] = '';
        }

        return implode("\n", $rst);
    }

    /**
     * {@inheritdoc}
     */
    protected function describeApplication(Application $application, array $options = array())
    {
        $describedNamespace = isset($options['namespace']) ? $options['namespace'] : null;
        $description = new ApplicationDescription($application, $describedNamespace);
        $blocks[] = 'Reference';
        $blocks[] = '=========';
        $blocks[] = '';

        foreach ($description->getNamespaces() as $namespace) {
            if (ApplicationDescription::GLOBAL_NAMESPACE !== $namespace['id']) {
                $blocks[] = $namespace['id'];
                $blocks[] = str_repeat('-', strlen($namespace['id']));
                $blocks[] = '';
            }

            $blocks[] = implode("\n", array_map(function ($commandName) {
                return '* :ref:`' . $this->getCommandRefName($commandName) . '`';
            } , $namespace['commands']));
            $blocks[] = '';
        }

        foreach ($description->getCommands() as $command) {
            $blocks[] = $this->describeCommand($command);
        }

        return implode("\n", $blocks);
    }
}
