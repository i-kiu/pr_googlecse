<?php

declare(strict_types=1);

namespace KronovaNet\PrGooglecse\Updates;

use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Upgrades\AbstractListTypeToCTypeUpdate;

#[UpgradeWizard('kronovanetPrGooglecseCTypeMigration')]
final class KronovaNetPrGooglecseCTypeMigration extends AbstractListTypeToCTypeUpdate
{
    public function getTitle(): string
    {
        return 'Migrate "KronovaNet PrGooglecse" plugins to content elements.';
    }

    public function getDescription(): string
    {
        return 'The "KronovaNet PrGooglecse" plugins are now registered as content element. Update migrates existing records and backend user permissions.';
    }

    /**
     * This must return an array containing the "list_type" to "CType" mapping.
     *
     *  Example:
     *
     *  [
     *      'pi_plugin1' => 'pi_plugin1',
     *      'pi_plugin2' => 'new_content_element',
     *  ]
     *
     * @return array<string, string>
     */
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'pr_googlecse' => 'pr_googlecse',
        ];
    }
}
