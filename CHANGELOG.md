Changelog
=========

alpha-3
-------

### Features

- [file]: `file:import` - New command to import files into the repository.
- [node]: `node:list` Added `--level` option to rescursively show children nodes and properties.
- [node]: `node:list` Show "unulfilled" property and child node definitions when listing node contents.

### Improvements

- [export]: `session:export:view`: Added `--pretty` option to `session:export:view` command to output formatted XML.
- [export]: `session:export:view`: Ask confirmation before overwriting file.
- [shell]: Autocomplete completes property names in addition to node names in current path.

### Bugs

- [shell]: Aliases do not allow quoted arguments.
- [shell]: Autocomplete causes segfault.
