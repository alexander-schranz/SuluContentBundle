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

namespace Sulu\Bundle\ContentBundle\Tests\Functional\Message;

use Sulu\Bundle\ContentBundle\Model\Content\Message\DuplicateContentMessage;
use Sulu\Bundle\ContentBundle\Tests\Functional\Traits\ContentDimensionTrait;
use Sulu\Bundle\ContentBundle\Tests\Functional\Traits\DimensionIdentifierTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class DuplicateContentMessageTest extends SuluTestCase
{
    use ContentDimensionTrait;
    use DimensionIdentifierTrait;

    public function setUp()
    {
        parent::setUp();

        $this->purgeDatabase();
    }

    public function testDuplicate(): void
    {
        $contentEN = $this->createDraftContentDimension(
            'test_resource_contents',
            'test-resource-1',
            'en',
            'default',
            ['title' => 'Sulu', 'article' => 'Sulu is awesome']
        );

        $contentDE = $this->createDraftContentDimension(
            'test_resource_contents',
            'test-resource-1',
            'de',
            'default',
            ['title' => 'Sulu', 'article' => 'Sulu ist toll!']
        );

        $message = new DuplicateContentMessage('test_resource_contents', 'test-resource-1', 'new-resource-1');
        $this->getMessageBus()->dispatch($message);

        $newContentEN = $this->findDraftContentDimension(
            'test_resource_contents',
            'new-resource-1',
            'en'
        );
        $newContentDE = $this->findDraftContentDimension(
            'test_resource_contents',
            'new-resource-1',
            'de'
        );

        $this->assertNotNull($newContentEN);
        $this->assertNotNull($newContentDE);

        $this->assertSame($contentEN->getResourceKey(), $newContentEN->getResourceKey());
        $this->assertSame($contentDE->getResourceKey(), $newContentDE->getResourceKey());

        $this->assertSame($contentEN->getDimensionIdentifier(), $newContentEN->getDimensionIdentifier());
        $this->assertSame($contentDE->getDimensionIdentifier(), $newContentDE->getDimensionIdentifier());

        $this->assertSame($contentEN->getType(), $newContentEN->getType());
        $this->assertSame($contentDE->getType(), $newContentDE->getType());

        $this->assertSame($contentEN->getData(), $newContentEN->getData());
        $this->assertSame($contentDE->getData(), $newContentDE->getData());
    }

    private function getMessageBus(): MessageBusInterface
    {
        /** @var MessageBusInterface $messageBus */
        $messageBus = $this->getContainer()->get('message_bus');

        return $messageBus;
    }
}
