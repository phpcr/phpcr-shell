Shell for PHPCR
---------------

[![Build Status](https://travis-ci.org/phpcr/phpcr-shell.png?branch=master)](https://travis-ci.org/phpcr/phpcr-shell)

Shell for PHPCR

## Building

The recommended way to use the PHPCR shell is as a phar archive.

Install box: http://box-project.org

Build the PHAR:

````bash
$ cd phpcr-shell
$ box build
````

This will produce the file `phpcr.phar`.

Copy this file to your bin directory:

````bash
$ sudo cp phpcrsh.phar /usr/bin/local/phpcrsh
````

## Connecting

To connect to a doctrine-dbal PHPCR repository:

    $ phpcr --transport=doctrine-dbal --db-name=foobar --db-username=user --db-password=foobar

Full definition:

````bash
Usage:
 phpcrsh [-h|--help] [-v|--verbose] [-V|--version] [--ansi] [--no-ansi] [-t|--transport="..."] [-pu|--phpcr-username="..."] [-pp|--phpcr-password[="..."]] [-pw|--phpcr-workspace[="..."]] [-du|--db-username="..."] [-dn|--db-name="..."] [-dp|--db-password[="..."]] [-dh|--db-host="..."] [-dd|--db-driver="..."] [-dP|--db-path="..."] [--no-interaction] [--unsupported] [-url|--repo-url="..."] [--command="..."]

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
 --no-interaction        Turn off interaction (for testing purposes)
 --unsupported           Show all commands, including commands not supported by the repository
 --repo-url (-url)       URL of repository (e.g. for jackrabbit). (default: "http://localhost:8080/server/")
 --command               Run the given command
````

## Navigating and manipulating the repository

You can navigate the repository using familiar filesystem shell commands:

````bash
PHPCRSH > ls
+-----------------+------------+-----------------+
| pocms/          | pocms:root |                 |
| jcr:primaryType | NAME       | nt:unstructured |
+-----------------+------------+-----------------+
PHPCRSH > cd pocms
PHPCRSH > pwd
/pocms
PHPCRSH > cd ..
PHPCRSH > pwd
/
PHPCRSH > cat jcr:primaryType
nt:unstructured
PHPCRSH > exit
````

The above commands are *aliases*. Aliases are defined in your home directory
in `~/.phpcrsh/aliases.yml`.

Aliases can be listed using the `alist` alias, or `shell:alias:list`.

The above commands would be expanded as:

````bash
PHPCRSH > node:list
PHPCRSH > shell:path:change pocms
PHPCRSH > shell:path:show
PHPCRSH > node:property:show jcr:primaryType
PHPCRSH > shell:exist
````

## Using profiles

Profiles enable you to save and reuse connection settings. Profiles can be
created or used by using the `--profile` option.

To create or update a profile, use it in conjunction with `--transport`, i.e.:

````bash
$ phpcrsh --profile=mydb --transport=doctrine-dbal --db-user=foobar --db-name=mydb 
Create new profile "mydb"?
````

To use the profile:

````bash
$ phpcrsh --profile=mydb
````

Or use the short syntax:

````bash
$ phpcrsh --pmydb
````


## Todo

- Better querying support
- Better autocompletion
- Directory aware configuration / configuration auto-detection
