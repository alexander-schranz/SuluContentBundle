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

use Sulu\Bundle\ContentBundle\Model\Seo\Message\RemoveSeoMessage;
use Sulu\Bundle\ContentBundle\Model\Seo\SeoDimensionRepositoryInterface;

class RemoveSeoMessageHandler
{
    /**
     * @var SeoDimensionRepositoryInterface
     */
    private $seoDimensionRepository;

    public function __construct(
        SeoDimensionRepositoryInterface $seoDimensionRepository
    ) {
        $this->seoDimensionRepository = $seoDimensionRepository;
    }

    public function __invoke(RemoveSeoMessage $message): void
    {
        $resourceKey = $message->getResourceKey();
        $resourceId = $message->getResourceId();

        $seoDimensions = $this->seoDimensionRepository->findByResource($resourceKey, $resourceId);

        foreach ($seoDimensions as $seoDimension) {
            $this->seoDimensionRepository->removeDimension($seoDimension);
        }
    }
}
