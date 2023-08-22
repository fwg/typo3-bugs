<?php

// normal case: extension overrides templates/partials/layouts from other ext
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(<<<TYPOSCRIPT
plugin.tx_bugsbase.view {
    partialRootPaths {
        0 = EXT:bugs_base/Resources/Private/Partials/
        10 = EXT:bugs_ext/Resources/Private/Partials/
    }
}
TYPOSCRIPT
);

// should work also: extension reuses base templates/partials
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'BugsExt',
    'Test',
    [
        \Fwg\BugsExt\Controller\TestController::class => 'index',
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(<<<TYPOSCRIPT
plugin.tx_bugsext.view {
    partialRootPaths {
        0 = EXT:bugs_base/Resources/Private/Partials/
        10 = EXT:bugs_ext/Resources/Private/Partials/
    }
}
TYPOSCRIPT
);
