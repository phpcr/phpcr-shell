Changelog
=========

1.5.0
-----

- If `XDG_DATA_HOME` is defined, it is now used as location where to store the history.
  If you have set `XDG_DATA_HOME` to something else than `HOME`, you can preserve your phpcr-shell history by moving the directory `~/.history_PHPCRSH` to `$XDB_DATA_HOME/.history_PHPCRSH`.

1.4.0
-----

- Support Symfony 6, drop PHP 7.4 support #210

1.3.0
-----

- Support PHP 8.0 #209

1.2.0
-----

- Support Symfony 5.0 #204

1.1.0
-----

- [cd] Allow trailing slash on path. #189
- [versioning] Fix node type check to use the PHPCR method for it. #190
- [node:list] Fix node counting. #188
- [query] Fix date formatting. #198
- [query] Remove header "Index" from the result table as there is never any data output. #197
- Fixed tests and styling, test with PHP 7. #194, #196

1.0.0
-----

Stable release

beta4
-----

### Bug fixes

- [query] Enforce termination of queries with ";": fixes #162
- [node:list] Single references not showing path
- [node:edit] Multivalue references encoded as arrays when editing
- [node:edit] Fixed undefined variable
- [version] Versioning commands can use relative paths
- [node:property:show] Text fields are truncated
- [profile] Workspace given from CLI does not override profile workspace
- [command] Removed node:definition command: Jackalope now supports this, and
  this command would never have worked, see:

beta2
-----

### Features

- [profile:show] Added command to display current profile
- [query:update] Introduced `expr()` function to allow setting poperty values using expression language.
- [node:list] Sort properties by default, added option to specify

### Bug fixes

- [embedded] No exit code returned
- [profile] Profile configuration overwritten by session parameters
- [node:list] Incorrect node count

beta1
-----

### BC Break

- [DoctrinePhpcrBundle] Shell must now be initiated in a different way in
  embedded mode. The DoctrinePhpcrBundle will need to be updated.
- [node:shared:remove] Removed this command and integrated it into
  `node:remove` instead (`node:remove . --shared`)

### Bug fixes

- [capability] Commands not being disabled based on capability
- [config] Do not override CLI options with profile options
- [node:remove] Cannot `node:remove` by UUID
- [node:edit] Serialization of single value references doesn't work
- [file:import] Irregular files are accepted
- [query] Possible to try and execute query without a query

### Enhancements

- [node:list] Show primary node type and super-types
- [node:list] Show property length
- [autocomplete] Autocomplete now includes command names
- [references] Show UUIDs when listing reference properties
- [import/export] Renamed session import and export to `session:import` &

### Features

- [cli] Specify workspace with first argument
- [config] Added user config for general settings
- [config] Enable / disable showing execution times and set decimal expansion
- [global] Refactored to use DI container and various general improvements
- [node:property:set] Allow setting reference property type by path`session:export`
- [node:references] Shows the referencing node paths instead of the referrered-to node path(s)
- [node:remove] Immediately fail when trying to delete a node which has a (hard) referrer
- [node] Added wilcard support to applicable node commands, including "node:list", "node:remove" and "node:property:show"
- [query:update] Added APPLY method to queries, permits addition and removal of mixins
- [transport] Added transport layer for experimental Jackalope FS implementation

alpha-6
-------

### Features

- [query] Full support for manipulating multivalue properties via functions.
          See http://phpcr.readthedocs.org/en/latest/phpcr-shell/querying.html

### Bug fixes

- [mixin] Node mxin remove does not accept a path
- [node:edit] Cannot edit nodes with multivalue References

### Enhancements

- [deps] The PHPCR implementations have been moved to require-dev
- [exit] Ask for confirmation before logging out when there are pending changes

alpha-5
-------

### Features

- [shell] Added "shell:clear" command to support clearing the console output
- [general] The shell supports being embedded as a dependency
- [node:edit] New command `node:edit` enables editing of entire node

### Bug Fixes

- [query] Disabled updating multivalue properties where properties have more
          than one value with the UPDATE, as currently other items are overwritten and
          data is lost. See: https://github.com/phpcr/phpcr-shell/issues/85
- [shell] Multivalue (and so multiline) property values are truncated as a single string (#70)

alpha-4
-------

### Features

- [node] copy,move and clone - Target paths automatically append basename if target is a node.
- [query] Always show path next to resultset
- [node|shell] Most commands which accept a node path can also accept a UUID
- [node] `node:list`: Show node primary item value
- [query] Support for UPDATE queries
- [query] Support for DELETE queries

### Bugs Fixes

- [args] 28 instances of bad InputArgument constructor fixed
- [node] `node:list` Catch exceptions when rendering property rows (e.g. on invalid references)

### Improvements

- [connect] Always expand relative paths for `db-path`
- [connect] Throw exception if file indicated by `db-path` does not exist.

alpha-3
-------

### Features

- [file] `file:import` - New command to import files into the repository.
- [node] `node:list` Added `--level` option to rescursively show children nodes and properties.
- [node] `node:list` Show "unulfilled" property and child node definitions when listing node contents.

### Improvements

- [export] `session:export:view`: Added `--pretty` option to `session:export:view` command to output formatted XML.
- [export] `session:export:view`: Ask confirmation before overwriting file.
- [shell] Autocomplete completes property names in addition to node names in current path.

### Bugs

- [shell] Aliases do not allow quoted arguments.
- [shell] Autocomplete causes segfault.
