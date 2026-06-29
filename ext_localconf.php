<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;

if (!defined('TYPO3')) {
    exit('Access denied.');
}

call_user_func(static function (): void {
    if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['pr_googlecse'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['pr_googlecse'] = [
            'frontend' => VariableFrontend::class,
            'backend' => Typo3DatabaseBackend::class,
            'options' => [],
            'groups' => [],
        ];
    }
});
