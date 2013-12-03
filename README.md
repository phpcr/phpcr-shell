Shell for PHPCR
---------------

Shell for PHPCR

## Conncting

To connect to a doctrine-dbal PHPCR repository:

    $ php bin/phpcr-shell --db_name=foobar --db_username=user --db_password=foobar

Full definition:

````bash
$ php bin/phpcr --help          
Usage:
 phpcr_shell [-h|--help] [-v|--verbose] [-V|--version] [--ansi] [--no-ansi] [-t|--transport="..."] [-pu|--phpcr_username="..."] [-pp|--phpcr_password[="..."]] [-pw|--phpcr_workspace[="..."]] [-du|--db_username="..."] [-dn|--db_name="..."] [-dp|--db_password[="..."]] [-dh|--db_host="..."] [-dd|--db_driver="..."] [-dP|--db_path="..."]

Options:
 --help (-h)             Display this help message.
 --verbose (-v)          Increase verbosity of messages.
 --version (-V)          Display this application version.
 --ansi                  Force ANSI output.
 --no-ansi               Disable ANSI output.
 --transport (-t)        Transport to use. (default: "doctrine-dbal")
 --phpcr_username (-pu)  PHPCR Username. (default: "admin")
 --phpcr_password (-pp)  PHPCR Password. (default: "admin")
 --phpcr_workspace (-pw) PHPCR Workspace. (default: "default")
 --db_username (-du)     Database Username. (default: "root")
 --db_name (-dn)         Database Name. (default: "phpcr")
 --db_password (-dp)     Database Password.
 --db_host (-dh)         Database Host. (default: "localhost")
 --db_driver (-dd)       Database Transport. (default: "pdo_mysql")
 --db_path (-dP)         Database Path.
````

## Executing Select Queries

JCR_SQL2 Select queries can be executed in the same way as in the MySQL shell:

````bash
PHPCR > select * from nt:unstructured;
0
  nt:unstructured
    /
    jcr:primaryType Name: nt:unstructured
1
  nt:unstructured
    /cms
    jcr:primaryType Name: nt:unstructured
    phpcr:class String: DTL\WebBundle\Document\Site
2
  nt:unstructured
    /cms/routes
    jcr:primaryType Name: nt:unstructured
...
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

All available commands:

````bash
Welcome to the PHPCR shell (1.0).

At the prompt, type help for some help,
or list to get a list of available commands.

To exit the shell, type ^D.

PHPCR > help
PHPCR version 1.0

Usage:
  [options] command [arguments]

Options:
  --help           -h Display this help message.
  --quiet          -q Do not output any message.
  --verbose        -v Increase verbosity of messages.
  --version        -V Display this application version.
  --ansi              Force ANSI output.
  --no-ansi           Disable ANSI output.
  --no-interaction -n Do not ask any interactive question.

Available commands:
  cd                 Change the current path
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
