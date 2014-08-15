Changelog
=========

dev-master
----------

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
