<?php

namespace PHPCR\Shell\Console\Wizard;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Wizard Interface
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
interface WizardInterface
{
    /**
     * Run the wizard
     *
     * @return mixed
     */
    public function run(InputInterface $input, OutputInterface $output);
}
