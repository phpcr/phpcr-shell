<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class NodeEditCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:edit');
        $this->setDescription('Edit the given node in the EDITOR configured by the system');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->setHelp(<<<HERE
Edit the given node
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $formatter = $this->getHelper('result_formatter');
        $path = $session->getAbsPath($input->getArgument('path'));

        $editor = $this->getHelper('editor');

        $skipBinary = true;
        $noRecurse = true;

        ob_start();
        $stream = fopen('php://output', 'w+', false);
        $session->exportSystemView($path, $stream, $skipBinary, $noRecurse);
        $out = ob_get_clean();
        fclose($stream);
        $out = $formatter->formatXml($out);

        $in = $editor->fromString($out);
    }
}

