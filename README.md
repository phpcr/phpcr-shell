Shell for PHPCR
---------------

Shell for PHPCR

## Conncting

To connect to a doctrine-dbal PHPCR repository:

    $ php bin/phpcr --db-name=foobar --db-username=user --db-password=foobar

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
PHPCR > SELECT * FROM nt:unstructured;
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
