# Reproduction for #101810

Passing a `FileReference` to `ContentObjectRenderer::getImgResource` should
respect the reference crop configuration.

Pertinent files:

* [Call to getImgResource in a ViewHelper](src/bugs_base/Classes/ViewHelpers/DumpImageInfoViewHelper.php)

Forge issue: [#101810](https://forge.typo3.org/issues/101810)

* `just ddev-10-4` or `just ddev-11-5` or `just ddev-12-4`
* `just reproduce`

## Requirements

* [ddev](https://ddev.com/)
* [jq](https://jqlang.github.io/jq/)
* [just](https://github.com/casey/just)
