Shell for PHPCR
---------------

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
PHPCR > select * from [n:unstructured]                                                                                                                         
| Row: #0 Score: 3
| Sel: nt:unstructured Path: /foobar/barfoo UID: none
+-----------------+--------+----------+-----------------+
| Name            | Type   | Multiple | Value           |
+-----------------+--------+----------+-----------------+
| phpcr           | String | no       | foo             |
| jcr:primaryType | Name   | no       | nt:unstructured |
+-----------------+--------+----------+-----------------+

| Row: #1 Score: 3
| Sel: nt:unstructured Path: /foo UID: none
+-----------------+------+----------+-----------------+
| Name            | Type | Multiple | Value           |
+-----------------+------+----------+-----------------+
| jcr:primaryType | Name | no       | nt:unstructured |
+-----------------+------+----------+-----------------+

| Row: #2 Score: 3
| Sel: nt:unstructured Path: /foobar UID: none
+-----------------+------+----------+-----------------+
| Name            | Type | Multiple | Value           |
+-----------------+------+----------+-----------------+
| jcr:primaryType | Name | no       | nt:unstructured |
+-----------------+------+----------+-----------------+
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

List all available commands with the `list` command:

````bash
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
