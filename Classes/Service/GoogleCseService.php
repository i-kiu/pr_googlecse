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
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Context\Context;

/**
 * Class GoogleCseService
 */
class GoogleCseService
{
    /**
     * @var string
     */
    private $url = 'https://www.googleapis.com/customsearch/v1?q=%s&cx=%s&key=%s&start=%d';

    /**
     * @var ExtConf
     */
    protected $extConf;

    /**
     * GoogleCseService constructor.
     */
    public function __construct(ExtConf $extConf)
    {
        $this->extConf = $extConf;
    }

    public function search(string $query, int $start, int $resultsPerPage): array
    {
        if($this->extConf->getFilterByCurrentLang()){
            $this->addLanguageParameterToUrl();
        }
        $requestUrl = sprintf(
            $this->getUrl(),
            urlencode($query),
            urlencode($this->extConf->getGoogleCseKey()),
            urlencode($this->extConf->getGoogleApiKey()),
            $start
        );
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $response = $requestFactory->request($requestUrl);
        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } else {
            throw new \HttpResponseException(
                'Your search could not be completed. HTTP response code: ' . $response->getStatusCode()
                . ', Response message: ' . $response->getBody()->getContents(),
                1527430897
            );
        }
    }
    private function getUrl(): string
    {
        return $this->url;
    } 
    private function setUrl(string $url): void
    {
        $this->url = $url;
    }
    private function getFrontendSelectedLanguage(): string
    {
        $context = GeneralUtility::makeInstance(Context::class);
        /** @var TYPO3\CMS\Core\Site\Entity\Site */
        $site = $GLOBALS['TYPO3_REQUEST']->getAttribute('site');
        $langId = $context->getPropertyFromAspect('language', 'id');
        /** @var TYPO3\CMS\Core\Site\Entity\SiteLanguage */
        $language = $site->getLanguageById($langId);
        $langCode = $language->getTwoLetterIsoCode();
        $currentLanguage = 'lang_'.$langCode;
        return '&lr='.$currentLanguage;
    }
    private function addLanguageParameterToUrl(): void
    {
        $this->setUrl($this->url.$this->getFrontendSelectedLanguage());
    }
}
