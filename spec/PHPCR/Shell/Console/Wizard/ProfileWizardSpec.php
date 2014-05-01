<?php

namespace spec\PHPCR\Shell\Console\Wizard;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Helper\DialogHelper;
use PHPCR\Shell\Console\Wizard\WizardInterface;
use PHPCR\Shell\Console\Helper\ProfileHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\Shell\Transport\SessionConfig;
use PHPCR\Shell\Config\Profile;
use PHPCR\Shell\Transport\TransportConfig;

class ProfileWizardSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Wizard\ProfileWizard');
    }

    function let(
        DialogHelper $dialogHelper,
        ProfileHelper $profileHelper,
        WizardInterface $connectionWizard
    )
    {
        $this->beConstructedWith(
            $dialogHelper,
            $profileHelper,
            $connectionWizard
        );
    }


    function it_should_return_a_config_object_for_a_named_profile(
        InputInterface $input,
        OutputInterface $output,
        Profile $profile,
        ProfileHelper $profileHelper,
        DialogHelper $dialogHelper
    )
    {
        $profileHelper->getProfileNames()->willReturn(array(
            'profile1', 'profile2'
        ));

        $output->writeln('(0) Use: profile1')->shouldBeCalled();
        $output->writeln('(1) Use: profile2')->shouldBeCalled();
        $output->writeln('(c) Create a new profile')->shouldBeCalled();

        $dialogHelper->ask($output, 'Enter a choice: ')->willReturn('0');

        $profileHelper->getProfile('profile1')->willReturn($profile);
        $profileHelper->getProfileNames()->willReturn(array(
            'profile1', 'profile2'
        ));

        $this->run($input, $output)->shouldReturn($profile);
    }

    function it_should_create_a_new_profile(
        InputInterface $input,
        OutputInterface $output,
        Profile $profile,
        ProfileHelper $profileHelper,
        DialogHelper $dialogHelper,
        TransportConfig $transportConfig,
        WizardInterface $connectionWizard
    )
    {
        $profileHelper->getProfileNames()->willReturn(array());

        $output->writeln('(c) Create a new profile')->shouldBeCalled();
        $dialogHelper->ask($output, 'Enter a choice: ')->willReturn('c');
        $dialogHelper->ask($output, 'Enter name for new profile: ')->willReturn('test');
        $connectionWizard->run($input, $output)->willReturn(array());
        $profileHelper->createNewProfile('test')->willReturn($profile);

        $this->run($input, $output)->shouldReturn($profile);
    }
}
