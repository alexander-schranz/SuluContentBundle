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

namespace Sulu\Bundle\ContentBundle\Model\Content\MessageHandler;

use Sulu\Bundle\ContentBundle\Model\Content\ContentDimensionRepositoryInterface;
use Sulu\Bundle\ContentBundle\Model\Content\Exception\ContentNotFoundException;
use Sulu\Bundle\ContentBundle\Model\Content\Message\DuplicateContentMessage;
use Sulu\Bundle\ContentBundle\Model\DimensionIdentifier\DimensionIdentifierInterface;
use Sulu\Bundle\ContentBundle\Model\DimensionIdentifier\DimensionIdentifierRepositoryInterface;

class DuplicateContentMessageHandler
{
    /**
     * @var ContentDimensionRepositoryInterface
     */
    private $contentDimensionRepository;

    /**
     * @var DimensionIdentifierRepositoryInterface
     */
    private $dimensionIdentifierRepository;

    public function __construct(
        ContentDimensionRepositoryInterface $contentDimensionRepository,
        DimensionIdentifierRepositoryInterface $dimensionIdentifierRepository
    ) {
        $this->contentDimensionRepository = $contentDimensionRepository;
        $this->dimensionIdentifierRepository = $dimensionIdentifierRepository;
    }

    public function __invoke(DuplicateContentMessage $message): void
    {
        $attributes = [
            DimensionIdentifierInterface::ATTRIBUTE_KEY_STAGE => DimensionIdentifierInterface::ATTRIBUTE_VALUE_DRAFT,
        ];

        $dimensionIdentifiers = $this->dimensionIdentifierRepository->findByPartialAttributes($attributes);
        if (!$dimensionIdentifiers) {
            return;
        }

        $contentDimensions = $this->contentDimensionRepository->findByDimensionIdentifiers(
            $message->getResourceKey(),
            $message->getResourceId(),
            $dimensionIdentifiers
        );
        if (!$contentDimensions) {
            if (!$message->isMandatory()) {
                return;
            }

            throw new ContentNotFoundException(['resourceKey' => $message->getResourceKey(), 'resourceId' => $message->getResourceId()]);
        }

        foreach ($contentDimensions as $contentDimension) {
            $this->contentDimensionRepository->createClone($contentDimension, $message->getNewResourceId());
        }
    }
}
