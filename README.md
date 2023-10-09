# Reproduction for #90600

When a processed file is re-generated due to a change in its checksum, for
example by having a newer mtime on its source file, any cached pages with a
previous version of the file will have broken URLs, as the processed file
automatically deletes the existing version.

This reproduction uses a cachePeriod of 1 second when a certain cookie is set,
thereby allowing the forced re-generation of the processed file.

See [TriggerBugCommand.php](src/bugs_base/Classes/Cli/TriggerBugCommand.php).

Forge issue: [#90600](https://forge.typo3.org/issues/90600)

* `just ddev-10-4` or `just ddev-11-5` or `just ddev-12-4`
* `just reproduce`

## Requirements

* [ddev](https://ddev.com/)
* [jq](https://jqlang.github.io/jq/)
* [just](https://github.com/casey/just)
