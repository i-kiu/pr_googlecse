<?php

declare(strict_types=1);

/*
 * This file is part of the package kronova/pr-googlecse.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace KronovaNet\PrGooglecse\Configuration;

use KronovaNet\PrGooglecse\Exception\IncompleteConfigurationException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * GoogleMapsController
 */
class ExtConf implements SingletonInterface
{
    /**
     * @var string
     */
    protected $googleApiKey = '';

    /**
     * @var string
     */
    protected $googleCseKey = '';

    /**
     * @var bool
     */
    protected $filterByCurrentLang = false;

    /**
     * @var bool
     */
    protected $enableCache = true;

    /**
     * @var int
     */
    protected $cacheLifetime = 300;

    /**
     * @var array
     */
    protected $requiredSettings = ['googleApiKey' => true, 'googleCseKey' => true, 'filterByCurrentLang' => false];

    /**
     * ExtConf constructor.
     */
    public function __construct()
    {
        // get global configuration
        $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('pr_googlecse');
        $missingRequiredSettings = $this->requiredSettings;
        if (is_array($extConf) && count($extConf)) {
            // call setter method foreach configuration entry
            foreach ($extConf as $key => $value) {
                $methodName = 'set' . ucfirst($key);
                if (method_exists($this, $methodName)) {
                    $reflectionMethod = new \ReflectionMethod($this, $methodName);
                    $type = $reflectionMethod->getParameters()[0]->getType();
                    if ($type) {
                        settype($value, $type->getName());
                    }
                    $this->$methodName($value);
                }
                if (array_key_exists($key, $missingRequiredSettings)) {
                    unset($missingRequiredSettings[$key]);
                }
            }
        }
        if (count($missingRequiredSettings)) {
            throw new IncompleteConfigurationException(
                'The following required settings are missing in your extension configuration: '
                . implode(', ', array_keys($missingRequiredSettings)),
                1527962959
            );
        }
    }

    public function getGoogleApiKey(): string
    {
        return $this->googleApiKey;
    }

    public function setGoogleApiKey(string $googleApiKey): void
    {
        $this->googleApiKey = trim((string)$googleApiKey);
    }

    public function getGoogleCseKey(): string
    {
        return $this->googleCseKey;
    }

    public function setGoogleCseKey(string $googleCseKey): void
    {
        $this->googleCseKey = $googleCseKey;
    }

    public function getFilterByCurrentLang(): bool
    {
        return $this->filterByCurrentLang;
    }

    public function setFilterByCurrentLang(bool $filterByCurrentLang): void
    {
        $this->filterByCurrentLang = $filterByCurrentLang;
    }

    public function isEnableCache(): bool
    {
        return $this->enableCache;
    }

    public function setEnableCache(bool $enableCache): void
    {
        $this->enableCache = (bool)$enableCache;
    }

    public function getCacheLifetime(): int
    {
        return $this->cacheLifetime;
    }

    public function setCacheLifetime(int $cacheLifetime): void
    {
        $this->cacheLifetime = $cacheLifetime;
    }
}
