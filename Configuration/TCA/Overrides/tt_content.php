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
