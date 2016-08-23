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

$dbConn = \Doctrine\DBAL\DriverManager::getConnection([
    'driver'    => 'pdo_sqlite',
    'dbname'    => 'test',
    'path'      => __DIR__.'/app.sqlite',
]);

/*
 * configuration
 */
$workspace = 'default'; // phpcr workspace to use
$user = 'admin';
$pass = 'admin';

$factory = new \Jackalope\RepositoryFactoryDoctrineDBAL();
$repository = $factory->getRepository(['jackalope.doctrine_dbal_connection' => $dbConn]);

$credentials = new \PHPCR\SimpleCredentials($user, $pass);

/* only create a session if this is not about the server control command */
if (isset($argv[1])
    && $argv[1] != 'jackalope:init:dbal'
    && $argv[1] != 'list'
    && $argv[1] != 'help'
) {
    $session = $repository->login($credentials, $workspace);

    $helperSet = new \Symfony\Component\Console\Helper\HelperSet([
        'dialog'               => new \Symfony\Component\Console\Helper\DialogHelper(),
        'phpcr'                => new \PHPCR\Util\Console\Helper\PhpcrHelper($session),
        'phpcr_console_dumper' => new \PHPCR\Util\Console\Helper\PhpcrConsoleDumperHelper(),
    ]);
} elseif (isset($argv[1]) && $argv[1] == 'jackalope:init:dbal') {
    // special case: the init command needs the db connection, but a session is impossible if the db is not yet initialized
    $helperSet = new \Symfony\Component\Console\Helper\HelperSet([
        'connection' => new \Jackalope\Tools\Console\Helper\DoctrineDbalHelper($dbConn),
    ]);
}
