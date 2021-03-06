<?php

declare(strict_types=1);

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ContentBundle\Model\Seo;

class SeoView implements SeoViewInterface
{
    /**
     * @var string
     */
    private $resourceKey;

    /**
     * @var string
     */
    private $resourceId;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string|null
     */
    private $title;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var string|null
     */
    private $keywords;

    /**
     * @var string|null
     */
    private $canonicalUrl;

    /**
     * @var bool
     */
    private $noIndex;

    /**
     * @var bool
     */
    private $noFollow;

    /**
     * @var bool
     */
    private $hideInSitemap;

    public function __construct(
        string $resourceKey,
        string $resourceId,
        string $locale,
        ?string $title = null,
        ?string $description = null,
        ?string $keywords = null,
        ?string $canonicalUrl = null,
        bool $noIndex = false,
        bool $noFollow = false,
        bool $hideInSitemap = false
    ) {
        $this->resourceKey = $resourceKey;
        $this->resourceId = $resourceId;
        $this->locale = $locale;
        $this->title = $title;
        $this->description = $description;
        $this->keywords = $keywords;
        $this->canonicalUrl = $canonicalUrl;
        $this->noIndex = $noIndex;
        $this->noFollow = $noFollow;
        $this->hideInSitemap = $hideInSitemap;
    }

    public function getResourceKey(): string
    {
        return $this->resourceKey;
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->canonicalUrl;
    }

    public function getNoIndex(): bool
    {
        return $this->noIndex;
    }

    public function getNoFollow(): bool
    {
        return $this->noFollow;
    }

    public function getHideInSitemap(): bool
    {
        return $this->hideInSitemap;
    }

    public function withResource(string $resourceKey, string $resourceId, string $locale): SeoViewInterface
    {
        return new static(
            $resourceKey,
            $resourceId,
            $locale,
            $this->title,
            $this->description,
            $this->keywords,
            $this->canonicalUrl,
            $this->noIndex ?? false,
            $this->noFollow ?? false,
            $this->hideInSitemap ?? false
        );
    }
}
