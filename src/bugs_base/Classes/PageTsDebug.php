<?php

namespace Fwg\BugsBase;

use TYPO3\CMS\Backend\Utility\BackendUtility;

class PageTsDebug
{
    public function render(): string
    {
        ob_start();

        echo "<p>TsConfig:</p>";
        echo '<code style="white-space: pre">' . file_get_contents(__DIR__ . '/../Configuration/TsConfig/Page.tsconfig') . '</code>';

        echo "<p>Page structure:</p>";
        echo <<<CODE
<pre>
1: site root
∟ 2: first subpage
∟ 3: second subpage
</pre>
CODE;

        $config = BackendUtility::getPagesTSconfig(1);
        echo "<p>TsConfig for page UID 1: some_property = {$config['some_property']}, should be 'no'</p>";

        $config = BackendUtility::getPagesTSconfig(2);
        echo "<p>TsConfig for page UID 2: some_property = {$config['some_property']}, should be 'yes'</p>";

        $config = BackendUtility::getPagesTSconfig(3);
        echo "<p>TsConfig for page UID 3: some_property = {$config['some_property']}, should be 'no'</p>";

        return ob_get_clean();
    }
}
