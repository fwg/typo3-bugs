<?php
/** @global $_EXTKEY */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Bugs Ext',
    'description' => 'Extension to reproduce a bug.',
    'category' => 'plugin',
    'author' => 'Friedemann Altrock',
    'author_email' => 'typo3@faltrock.de',
    'state' => 'beta',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-12.4.99',
            'bugs_base' => '*',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
