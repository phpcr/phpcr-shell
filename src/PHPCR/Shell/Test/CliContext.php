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

namespace PHPCR\Shell\Test;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use PHPUnit\Framework\Assert;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Features context.
 */
class CliContext implements Context, SnippetAcceptingContext
{
    private $output = [];
    private $rootPath;
    private $lastExitCode;
    private $fixturesDir;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @BeforeScenario
     */
    public function beforeScenario()
    {
        $this->output = [];

        $this->rootPath = realpath(__DIR__.'/../../../..');
        $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'phpcr-shell'.DIRECTORY_SEPARATOR.
            md5(microtime(true) * rand(0, 10000));
        $this->fixturesDir = realpath(__DIR__.'/../../../../features/fixtures/');

        $this->workingDir = $dir;
        putenv('PHPCRSH_HOME='.$dir);

        mkdir($this->workingDir, 0777, true);
        mkdir($this->workingDir.'/profiles');
        chdir($this->workingDir);
        $this->filesystem = new Filesystem();
    }

    /**
     * Cleans test folders in the temporary directory.
     *
     * @AfterSuite
     */
    public static function cleanTestFolders()
    {
        $fs = new Filesystem();
        $fs->remove(sys_get_temp_dir().DIRECTORY_SEPARATOR.'phpcr-shell');
    }

    private function exec($command)
    {
        exec($command, $output, $return);
        $this->output += $output;
        $this->lastExitCode = $return;
    }

    /**
     * @Given I run PHPCR shell with ":argsAndOptions"
     */
    public function execShell($argsAndOptions)
    {
        $this->exec(sprintf('%s/bin/phpcrsh %s', $this->rootPath, $argsAndOptions));
    }

    /**
     * @Given print output
     */
    public function printOutput()
    {
        foreach ($this->output as $line) {
            echo $line.PHP_EOL;
        }
    }

    /**
     * @Given the following profile ":profileName" exists:
     */
    public function iHaveTheFollowingProfile($profileName, PyStringNode $text)
    {
        file_put_contents($this->workingDir.'/profiles/'.$profileName.'.yml', $text->getRaw());
    }

    /**
     * @Given I initialize doctrine dbal
     */
    public function initializeDoctrineDbal()
    {
        $this->filesystem->copy(
            $this->fixturesDir.'/jackalope-doctrine-dbal-cli-config.php',
            $this->workingDir.'/cli-config.php'
        );
        $this->exec($this->rootPath.'/vendor/jackalope/jackalope-doctrine-dbal/bin/jackalope jackalope:init:dbal --force');
    }

    /**
     * @Then the command should not fail
     */
    public function theCommandShouldNotFail()
    {
        Assert::assertEquals(0, $this->lastExitCode);
    }
}
