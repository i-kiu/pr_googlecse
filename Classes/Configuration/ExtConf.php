<?php

declare(strict_types=1);

/*
 * This file is part of the package kronova/pr-googlecse.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace KronovaNet\PrGooglecse\Configuration;

use ReflectionMethod;
use ReflectionNamedType;
use KronovaNet\PrGooglecse\Exception\IncompleteConfigurationException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ExtConf
{
    protected string $googleApiKey = '';

    protected string $googleCseKey = '';

    protected bool $filterByCurrentLang = false;

    protected bool $enableCache = true;

    protected int $cacheLifetime = 300;

    /**
     * @var array<string, bool>
     */
    protected array $requiredSettings = [
        'googleApiKey' => true,
        'googleCseKey' => true,
        'filterByCurrentLang' => false,
    ];

    public function __construct(
        private readonly ExtensionConfiguration $extensionConfiguration,
    ) {
        $extConf = $this->extensionConfiguration->get('pr_googlecse');
        if (!\is_array($extConf) || $extConf === []) {
            return;
        }

        foreach ($extConf as $key => $value) {
            $methodName = 'set' . ucfirst((string)$key);
            if (!method_exists($this, $methodName)) {
                continue;
            }

            $reflectionMethod = new ReflectionMethod($this, $methodName);
            $type = $reflectionMethod->getParameters()[0]->getType();
            if ($type instanceof ReflectionNamedType) {
                settype($value, $type->getName());
            }

            $this->$methodName($value);
        }
    }

    public function assertConfigured(): void
    {
        $missing = [];
        if ($this->googleApiKey === '') {
            $missing[] = 'googleApiKey';
        }
        if ($this->googleCseKey === '') {
            $missing[] = 'googleCseKey';
        }

        if ($missing !== []) {
            throw new IncompleteConfigurationException(
                'The following required settings are missing in your extension configuration: '
                . implode(', ', $missing),
                1527962959,
            );
        }
    }

    public function getGoogleApiKey(): string
    {
        return $this->googleApiKey;
    }

    public function setGoogleApiKey(string $googleApiKey): void
    {
        $this->googleApiKey = trim($googleApiKey);
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
        $this->enableCache = $enableCache;
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
