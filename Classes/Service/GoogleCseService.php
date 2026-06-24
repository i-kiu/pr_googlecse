<?php

declare(strict_types=1);

/*
 * This file is part of the package kronova/pr-googlecse.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace KronovaNet\PrGooglecse\Service;

use KronovaNet\PrGooglecse\Configuration\ExtConf;
use KronovaNet\PrGooglecse\Exception\SearchApiException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Site\Entity\Site;

class GoogleCseService
{
    private const string SEARCH_URL = 'https://www.googleapis.com/customsearch/v1?q=%s&cx=%s&key=%s&start=%d&num=%d';

    public function __construct(
        private readonly ExtConf $extConf,
        private readonly RequestFactory $requestFactory,
        private readonly Context $context,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function search(string $query, int $start, int $resultsPerPage, ServerRequestInterface $request): array
    {
        $this->extConf->assertConfigured();

        $requestUrl = \sprintf(
            self::SEARCH_URL,
            urlencode($query),
            urlencode($this->extConf->getGoogleCseKey()),
            urlencode($this->extConf->getGoogleApiKey()),
            $start,
            $resultsPerPage,
        );

        if ($this->extConf->getFilterByCurrentLang()) {
            $requestUrl .= $this->buildLanguageParameter($request);
        }

        $response = $this->requestFactory->request($requestUrl, 'GET', ['timeout' => 5]);

        return $this->decodeResponse($response);
    }

    private function buildLanguageParameter(ServerRequestInterface $request): string
    {
        $site = $request->getAttribute('site');
        if (!$site instanceof Site) {
            return '';
        }

        $langId = (int)$this->context->getPropertyFromAspect('language', 'id');
        $language = $site->getLanguageById($langId);

        return '&lr=lang_' . substr($language->getLocale()->getLanguageCode(), 0, 2);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeResponse(ResponseInterface $response): array
    {
        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);
        }

        throw new SearchApiException(
            'Your search could not be completed. HTTP response code: ' . $response->getStatusCode()
            . ', Response message: ' . $response->getBody()->getContents(),
            1527430897,
        );
    }
}
