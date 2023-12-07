# Reproduction for #102622

When a user who is limited to certain languages opens the List module on a page
which is not contained in the subtree of a site root, an exception occurs.

Forge issue: [#102622](https://forge.typo3.org/issues/102622)

* `just ddev-11-5` or `just ddev-12-4`
* `just reproduce`

## Requirements

* [ddev](https://ddev.com/)
* [jq](https://jqlang.github.io/jq/)
* [just](https://github.com/casey/just)
