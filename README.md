# Reproduction for #101190

A content element with multiple RTE instances but different RTE configurations
will possibly not save the correct markup for all fields.

Pertinent files:

* [RTE config with additional processing/allowedTags](src/bugs_base/Configuration/RTE/MinimalPlus.yaml)
* [tt_content element with two RTE configs](src/bugs_base/Configuration/TCA/Overrides/tt_content.php)

Forge issue: [#101190](https://forge.typo3.org/issues/101190)

* `just ddev-10-4` or `just ddev-11-5` or `just ddev-12-4`
* `just reproduce`

## Requirements

* [ddev](https://ddev.com/)
* [jq](https://jqlang.github.io/jq/)
* [just](https://github.com/casey/just)
