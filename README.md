# Reproduction for #91274

Some installations of ImageMagick 7 produce wrong images because the result is
not of `-type TrueType`. This is visible in the `Environment` module's image
processing test.

Forge issue: [#91274](https://forge.typo3.org/issues/91274)

Targets for `just`:

* `ddev-13-4`: TYPO3 v13.4 under DDEV with an extra Dockerfile to compile IM7
* `alpine-stock`: TYPO3 v13.4 under `php -S` with Alpine 3.21 and stock IM7
* `alpine-compile`: TYPO3 v13.4 under `php -S` with Alpine 3.21 and compiled IM7

For the alpine based targets you need to run through the initial TYPO3 setup,
the ddev one already has the setup step done on the CLI (user/pw see `justfile`).

Note: the `src/` directory can be ignored for this bug.

## Requirements

* some Docker-compatible container runner, `docker` CLI
* [ddev](https://ddev.com/) if you want to test the DDEV environment
* [jq](https://jqlang.github.io/jq/)
* [just](https://github.com/casey/just)
