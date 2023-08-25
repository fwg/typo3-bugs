<?php

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        'Test Element with 2 RTEs',
        'bugsbase_test',
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'bodytext2' => [
        'label' => 'Bodytext 2',
        'config' => $GLOBALS['TCA']['tt_content']['columns']['bodytext']['config'],
    ],
]);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tt_content', 'bodytext2', 'text', 'after:bodytext');

$GLOBALS['TCA']['tt_content']['types']['text']['columnsOverrides']['bodytext2']['config']['enableRichtext'] = 1;

$GLOBALS['TCA']['tt_content']['columns']['bodytext']['config']['richtextConfiguration'] = 'minimal';
$GLOBALS['TCA']['tt_content']['columns']['bodytext2']['config']['richtextConfiguration'] = 'minimal-plus';
