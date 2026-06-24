<?php

declare(strict_types=1);

/*
 * This file is part of the package kronova/pr-googlecse.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace KronovaNet\PrGooglecse\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class SearchResultCountViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('totalResults', 'integer', 'Amount of total results', true);
        $this->registerArgument('resultsPerPage', 'integer', 'Results per page to calculate the start index', true);
        $this->registerArgument('maxPagesToDisplay', 'integer', 'Max amount of pages to display', false, 10);
        $this->registerArgument('startIndex', 'integer', 'The current start index', false, 10);
    }

    /**
     * @return array<int, int>
     */
    public function render(): array
    {
        $totalResults = (int)$this->arguments['totalResults'];
        $resultsPerPage = (int)$this->arguments['resultsPerPage'];
        $maxPagesToDisplay = (int)$this->arguments['maxPagesToDisplay'];
        $startIndex = (int)$this->arguments['startIndex'];

        $totalPages = $totalResults / $resultsPerPage;
        $currentPage = (int)round($startIndex / $resultsPerPage);
        $lastPage = $totalPages > $maxPagesToDisplay ? $maxPagesToDisplay : $totalPages;
        $page = $currentPage > ($totalPages * 0.6) ? (int)round($totalPages * 0.4) : 1;
        $pages = [];
        for ($page; $page <= $lastPage; ++$page) {
            $pages[$page] = $resultsPerPage * $page;
        }

        return $pages;
    }
}
