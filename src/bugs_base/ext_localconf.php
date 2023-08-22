<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'BugsBase',
    'Test',
    [
        \Fwg\BugsBase\Controller\TestController::class => 'index',
    ]
);
