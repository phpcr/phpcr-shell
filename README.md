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

## Executing Select Queries

JCR_SQL2 Select queries can be executed in the same way as in the MySQL shell:

````bash
PHPCR > SELECT * FROM [nt:unstructured];

| Row: #13 Score: 3
| Sel: nt:unstructured Path: /functional/Lyon_65019 UID: e6e74fdb-d329-4405-abd6-317bd0a9a325
+--------------------+--------+----------+--------------------------------------+
| Name               | Type   | Multiple | Value                                |
+--------------------+--------+----------+--------------------------------------+
| phpcr:classparents | String | yes      |                                      |
| phpcr:class        | String | no       | Doctrine\Tests\Models\CMS\CmsAddress |
| jcr:uuid           | String | no       | e6e74fdb-d329-4405-abd6-317bd0a9a325 |
| jcr:mixinTypes     | Name   | yes      | [0] phpcr:managed                    |
|                    |        |          | [1] mix:referenceable                |
| country            | String | no       | France                               |
| jcr:primaryType    | Name   | no       | nt:unstructured                      |
| city               | String | no       | Lyon                                 |
| zip                | String | no       | 65019                                |
+--------------------+--------+----------+--------------------------------------+

| Row: #14 Score: 3
| Sel: nt:unstructured Path: /functional/anonymous UID: 40d35c13-083a-447d-a86a-04bfee2f0608
+--------------------+--------+----------+--------------------------------------+
| Name               | Type   | Multiple | Value                                |
+--------------------+--------+----------+--------------------------------------+
| phpcr:class        | String | no       | Doctrine\Tests\Models\CMS\CmsUser    |
| phpcr:classparents | String | yes      |                                      |
| jcr:uuid           | String | no       | 40d35c13-083a-447d-a86a-04bfee2f0608 |
| jcr:mixinTypes     | Name   | yes      | [0] phpcr:managed                    |
|                    |        |          | [1] mix:referenceable                |
| username           | String | no       | anonymous                            |
| jcr:primaryType    | Name   | no       | nt:unstructured                      |
+--------------------+--------+----------+--------------------------------------+

2 rows in set (0.04 sec)
````

## Changing the CWD

The PHPCR shell allows you to navigate the PHPCR document hierarchy like a file system

````bash
PHPCR > ls
ROOT:
    cms/
        foobar/
            some-node
PHPCR > cd cms/foobar
PHPCR > pwd
/cms/foobar
PHPCR > mv foobar /barfoo
````

## All other commands

The PHPCR shell wraps all the commands of the `phpcr-utils` package, list
them with the `list` command:

````bash
PHPCR> list
Available commands:
  cd                 Change the current path
  exit               Logout and quit the shell
  help               Displays help for a command
  list               Lists commands
  ls                 Alias for dump
  mv                 Moves a node from one path to another
  nt-list            List all available node types in the repository
  nt-register        Register node types in the PHPCR repository
  pwd                Print Working Directory (or path)
  rm                 Remove content from the repository
  select             Execute an JCR_SQL2 query.
  touch              Create or modify a node
  update             Command to manipulate the nodes in the workspace.
  workspace-create   Create a workspace in the configured repository
  workspace-delete   Delete a workspace from the configured repository
  workspace-export   Export nodes from the repository, either to the JCR system view format or the document view format
  workspace-import   Import xml data into the repository, either in JCR system view format or arbitrary xml
  workspace-list     List all available workspaces in the configured repository
  workspace-purge    Remove all nodes from a workspace
````
