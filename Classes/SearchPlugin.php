<?php

declare(strict_types=1);

/*
 * This file is part of the package kronova/pr-googlecse.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace KronovaNet\PrGooglecse;

use Exception;
use KronovaNet\PrGooglecse\Configuration\ExtConf;
use KronovaNet\PrGooglecse\Service\GoogleCseService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Attribute\AsAllowedCallable;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class SearchPlugin
{
    public ContentObjectRenderer $cObj;

    public function __construct(
        private readonly GoogleCseService $googleCseService,
        private readonly ExtConf $extConf,
        private readonly FrontendInterface $cache,
        private readonly LoggerInterface $logger,
        private readonly ViewFactoryInterface $viewFactory,
    ) {
    }

    /**
     * @param array<string, mixed> $conf
     */
    #[AsAllowedCallable]
    public function render(string $_, array $conf): string
    {
        $request = $this->cObj->getRequest();
        $query = trim($this->getRequestParameter($request, 'prGoogleCseQuery'));
        $start = (int)$this->getRequestParameter($request, 'prGoogleCseStartIndex') ?: 1;
        $pageUid = (int)$request->getAttribute('frontend.page.information')?->getId();
        $pageType = (int)$this->getRequestParameter($request, 'type');
        $resultsPerPage = (int)($conf['resultsPerPage'] ?? 10);
        $shouldUseCache = $this->extConf->isEnableCache() && $query !== '';
        $skipCacheForThisRequest = false;
        $cacheIdentifier = $this->buildCacheIdentifier($query, $start, $resultsPerPage, $conf, $request);

        if ($shouldUseCache && $this->cache->has($cacheIdentifier)) {
            return (string)$this->cache->get($cacheIdentifier);
        }

        $view = $this->createView($request);
        $view->assignMultiple([
            'pageUid' => $pageUid,
            'pageType' => $pageType,
        ]);

        if ($query !== '') {
            try {
                $response = $this->googleCseService->search($query, $start, $resultsPerPage, $request);
                $view->assignMultiple([
                    'response' => $response,
                    'prGoogleCseQuery' => $query,
                    'resultsPerPage' => $resultsPerPage,
                    'showPagesInPagination' => (bool)($conf['showPagesInPagination'] ?? false),
                ]);
                $content = $view->render('Search/Results');
            } catch (Exception $exception) {
                $skipCacheForThisRequest = true;
                $this->logger->error('Exception during search!', ['exception' => $exception]);
                $content = $view->render('Search/Error');
            }
        } else {
            $content = $view->render('Search/Form');
        }

        if ($shouldUseCache && !$skipCacheForThisRequest) {
            $this->cache->set($cacheIdentifier, $content, [], $this->extConf->getCacheLifetime());
        }

        return $content;
    }

    private function createView(ServerRequestInterface $request): ViewInterface
    {
        return $this->viewFactory->create(new ViewFactoryData(
            templateRootPaths: ['EXT:pr_googlecse/Resources/Private/Templates'],
            partialRootPaths: ['EXT:pr_googlecse/Resources/Private/Partials'],
            layoutRootPaths: ['EXT:pr_googlecse/Resources/Private/Layouts'],
            request: $request,
        ));
    }

    private function getRequestParameter(ServerRequestInterface $request, string $name): string
    {
        $parsedBody = $request->getParsedBody();
        if (\is_array($parsedBody) && isset($parsedBody[$name])) {
            return (string)$parsedBody[$name];
        }

        return (string)($request->getQueryParams()[$name] ?? '');
    }

    /**
     * @param array<string, mixed> $conf
     */
    private function buildCacheIdentifier(
        string $query,
        int $start,
        int $resultsPerPage,
        array $conf,
        ServerRequestInterface $request,
    ): string {
        return md5(implode('|', [
            $query,
            (string)$start,
            (string)$resultsPerPage,
            $this->resolveLocale($request),
            (string)(int)($conf['showPagesInPagination'] ?? false),
        ]));
    }

    private function resolveLocale(ServerRequestInterface $request): string
    {
        $language = $request->getAttribute('language');
        if ($language instanceof SiteLanguage) {
            return (string)$language->getLocale();
        }

        $site = $request->getAttribute('site');
        if ($site !== null) {
            return (string)$site->getDefaultLanguage()->getLocale();
        }

        return '';
    }

    public function setContentObjectRenderer(ContentObjectRenderer $cObj): void
    {
        $this->cObj = $cObj;
    }
}
