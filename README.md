# Reproduction for #104318

`BackendUtility::getPageTSconfig` should return different results when the PageTS
configuration contains conditions for the current page ID.

Run this:

* `just ddev-12-4` or `just ddev-11-5`
* `just reproduce`

It should work in 11.5 but not in 12.4.

Pertinent files:

* [Page.tsconfig](src/bugs_base/Configuration/TsConfig/Page.tsconfig)
* [BackendUtility use](src/bugs_base/Classes/PageTsDebug.php)

Forge issue: [#104318](https://forge.typo3.org/issues/104318)

## Requirements

* [ddev](https://ddev.com/)
* [jq](https://jqlang.github.io/jq/)
* [just](https://github.com/casey/just)
