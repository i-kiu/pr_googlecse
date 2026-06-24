<?php

declare(strict_types=1);

namespace KronovaNet\PrGooglecse\Tests\Unit\ViewHelpers;

use KronovaNet\PrGooglecse\ViewHelpers\SearchResultCountViewHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;

final class SearchResultCountViewHelperTest extends TestCase
{
    #[Test]
    public function renderBuildsPageIndexMap(): void
    {
        $viewHelper = new SearchResultCountViewHelper();
        $viewHelper->setRenderingContext(new RenderingContext());
        $viewHelper->initializeArguments();
        $viewHelper->setArguments([
            'totalResults' => 100,
            'resultsPerPage' => 10,
            'maxPagesToDisplay' => 5,
            'startIndex' => 10,
        ]);

        $pages = $viewHelper->render();

        self::assertNotEmpty($pages);
        self::assertSame(10, reset($pages));
    }
}
