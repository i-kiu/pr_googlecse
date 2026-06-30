<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// Register frontend plugin
ExtensionManagementUtility::addPlugin(
    [
        'LLL:EXT:pr_googlecse/Resources/Private/Language/locallang_db.xlf:plugin.title',
        'pr_googlecse',
        'EXT:pr_googlecse/Resources/Public/Icons/Extension.svg',
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

// pr_googlecse has no plugin flexform; disable inherited tt_content.pi_flexform flex handling (TYPO3 v14).
$GLOBALS['TCA']['tt_content']['types']['pr_googlecse']['columnsOverrides']['pi_flexform'] = [
    'config' => [
        'type' => 'passthrough',
    ],
];
