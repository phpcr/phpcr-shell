<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class NodeShowCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:show');
        $this->setDescription('Show a node');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to source node');
        $this->setHelp(<<<HERE
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $absPath = $session->getAbsPath($input->getArgument('path'));
        $formatter = $this->getHelper('result_formatter');
        $highlighter = $this->getHelper('syntax_highlighter');

        $skipBinary = true;
        $noRecurse = true;

        ob_start();
        $stream = fopen('php://output', 'w+', false);
        $session->exportSystemView($absPath, $stream, $skipBinary, $noRecurse);
        $out = ob_get_clean();
        fclose($stream);

        $output->writeln($highlighter->highlightXml($formatter->formatXml($out)));
    }
}
