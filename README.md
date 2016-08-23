Shell for PHPCR
---------------

[![Build Status](https://travis-ci.org/phpcr/phpcr-shell.png?branch=master)](https://travis-ci.org/phpcr/phpcr-shell)
[![StyleCI](https://styleci.io/repos/14844492/shield)](https://styleci.io/repos/14844492)

Shell for PHPCR

## Building

The recommended way to use the PHPCR shell is as a phar archive.

Install box: https://box-project.github.io/box2/

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

## Documentation

Read the documentation on [readthedocs](http://phpcr.readthedocs.org/en/latest/phpcr-shell/index.html)
