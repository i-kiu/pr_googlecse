<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile(
    'pr_googlecse',
    'Configuration/TypoScript',
    'Google Custom Search Basic Template'
);
