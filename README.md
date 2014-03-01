Shell for PHPCR
---------------

[![Build Status](https://travis-ci.org/phpcr/phpcr-shell.png?branch=master)](https://travis-ci.org/phpcr/phpcr-shell)

Shell for PHPCR

## Building

The recommended way to use the PHPCR shell is as a phar archive.

Currently there is no stable release and so it is necessary to build it manually.

Install box: http://box-project.org

Build the PHAR:

````bash
$ cd phpcr-shell
$ box build
````

This will produce the file `phpcr.phar`.

Copy this file to your bin directory:

````bash
$ sudo cp phpcr.sh /usr/bin
````

## Connecting

To connect to a doctrine-dbal PHPCR repository:

    $ phpcr --db-name=foobar --db-username=user --db-password=foobar

Full definition:

````bash
./bin/phpcr --help
Usage:
 phpcr_shell [-h|--help] [-v|--verbose] [-V|--version] [--ansi] [--no-ansi] [-t|--transport="..."] [-pu|--phpcr-username="..."] [-pp|--phpcr-password[="..."]] [-pw|--phpcr-workspace[="..."]] [-du|--db-username="..."] [-dn|--db-name="..."] [-dp|--db-password[="..."]] [-dh|--db-host="..."] [-dd|--db-driver="..."] [-dP|--db-path="..."] [-url|--repo-url="..."]

Options:
 --help (-h)             Display this help message.
 --verbose (-v)          Increase verbosity of messages.
 --version (-V)          Display this application version.
 --ansi                  Force ANSI output.
 --no-ansi               Disable ANSI output.
 --transport (-t)        Transport to use. (default: "doctrine-dbal")
 --phpcr-username (-pu)  PHPCR Username. (default: "admin")
 --phpcr-password (-pp)  PHPCR Password. (default: "admin")
 --phpcr-workspace (-pw) PHPCR Workspace. (default: "default")
 --db-username (-du)     Database Username. (default: "root")
 --db-name (-dn)         Database Name. (default: "phpcr")
 --db-password (-dp)     Database Password.
 --db-host (-dh)         Database Host. (default: "localhost")
 --db-driver (-dd)       Database Transport. (default: "pdo_mysql")
 --db-path (-dP)         Database Path.
 --repo-url (-url)       URL of repository (e.g. for jackrabbit). (default: "http://localhost:8080/server/")
````

## TODO

- Versioning:
  - Activity
  - Configuration

