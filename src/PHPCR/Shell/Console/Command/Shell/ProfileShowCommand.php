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

namespace PHPCR\Shell\Console\Command\Shell;

use PHPCR\Shell\Console\Command\BaseCommand;
use PHPCR\Shell\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProfileShowCommand extends BaseCommand
{
    protected $output;

    public function configure()
    {
        $this->setName('shell:profile:show');
        $this->setDescription('Show the current profile configuration');
        $this->setHelp(<<<'EOT'
Display the currently loaded profile configuration
EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $profile = $this->get('config.profile');

        $output->writeln('<comment>NOTE: The profile may include information not relating to your current transport</comment>');
        $output->writeln('');
        foreach ($profile->toArray() as $domain => $config) {
            $output->writeln('<comment>'.$domain.'</comment>');
            $table = new Table($output);
            $table->setHeaders(['Key', 'Value']);

            foreach ($config as $key => $value) {
                if ($key === 'db_password') {
                    $value = '***';
                }

                $table->addRow([
                    $key,
                    is_scalar($value) ? $value : json_encode($value),
                ]);
            }
            $table->render($output);
        }
    }
}
