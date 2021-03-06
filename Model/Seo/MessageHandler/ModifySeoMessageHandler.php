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

namespace Sulu\Bundle\ContentBundle\Model\Seo\MessageHandler;

use Sulu\Bundle\ContentBundle\Model\DimensionIdentifier\DimensionIdentifierInterface;
use Sulu\Bundle\ContentBundle\Model\DimensionIdentifier\DimensionIdentifierRepositoryInterface;
use Sulu\Bundle\ContentBundle\Model\Seo\Exception\SeoNotFoundException;
use Sulu\Bundle\ContentBundle\Model\Seo\Factory\SeoViewFactoryInterface;
use Sulu\Bundle\ContentBundle\Model\Seo\Message\ModifySeoMessage;
use Sulu\Bundle\ContentBundle\Model\Seo\SeoDimensionInterface;
use Sulu\Bundle\ContentBundle\Model\Seo\SeoDimensionRepositoryInterface;

class ModifySeoMessageHandler
{
    /**
     * @var SeoDimensionRepositoryInterface
     */
    private $seoDimensionRepository;

    /**
     * @var DimensionIdentifierRepositoryInterface
     */
    private $dimensionIdentifierRepository;

    /**
     * @var SeoViewFactoryInterface
     */
    private $seoViewFactory;

    public function __construct(
        SeoDimensionRepositoryInterface $seoDimensionRepository,
        DimensionIdentifierRepositoryInterface $dimensionIdentifierRepository,
        SeoViewFactoryInterface $seoViewFactory
    ) {
        $this->seoDimensionRepository = $seoDimensionRepository;
        $this->dimensionIdentifierRepository = $dimensionIdentifierRepository;
        $this->seoViewFactory = $seoViewFactory;
    }

    public function __invoke(ModifySeoMessage $message): void
    {
        $localizedDraftSeo = $this->findOrCreateDraftSeoDimension(
            $message->getResourceKey(),
            $message->getResourceId(),
            $message->getLocale()
        );
        $this->setData($message, $localizedDraftSeo);

        $seoView = $this->seoViewFactory->create([$localizedDraftSeo], $message->getLocale());
        if (!$seoView) {
            throw new SeoNotFoundException(['resourceKey' => $message->getResourceKey(), 'resourceId' => $message->getResourceId()]);
        }

        $message->setSeo($seoView);
    }

    private function setData(
        ModifySeoMessage $message,
        SeoDimensionInterface $localizedDraftSeo
    ): void {
        $localizedDraftSeo->setTitle($message->getTitle());
        $localizedDraftSeo->setDescription($message->getDescription());
        $localizedDraftSeo->setKeywords($message->getKeywords());
        $localizedDraftSeo->setCanonicalUrl($message->getCanonicalUrl());
        $localizedDraftSeo->setNoIndex($message->getNoIndex());
        $localizedDraftSeo->setNoFollow($message->getNoFollow());
        $localizedDraftSeo->setHideInSitemap($message->getHideInSitemap());
    }

    private function findOrCreateDraftSeoDimension(
        string $resourceKey,
        string $resourceId,
        string $locale
    ): SeoDimensionInterface {
        $dimensionIdentifier = $this->getDraftDimensionIdentifier($locale);

        return $this->seoDimensionRepository->findOrCreateDimension($resourceKey, $resourceId, $dimensionIdentifier);
    }

    private function getDraftDimensionIdentifier(string $locale): DimensionIdentifierInterface
    {
        $attributes = [];
        $attributes[DimensionIdentifierInterface::ATTRIBUTE_KEY_STAGE] = DimensionIdentifierInterface::ATTRIBUTE_VALUE_DRAFT;
        $attributes[DimensionIdentifierInterface::ATTRIBUTE_KEY_LOCALE] = $locale;

        return $this->dimensionIdentifierRepository->findOrCreateByAttributes($attributes);
    }
}
