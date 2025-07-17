<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Pimcore\DataObject;

use CoreShop\Component\Pimcore\Exception\LinkGenerationNotPossibleException;
use CoreShop\Component\Pimcore\PriorityQueue;
use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;

class CompositeLinkGenerator implements LinkGeneratorInterface
{
    /**
     * @var PriorityQueue|LinkGeneratorInterface[]
     *
     * @psalm-var PriorityQueue<LinkGeneratorInterface>
     */
    private PriorityQueue $linkGenerator;

    public function __construct(
        ) {
        $this->linkGenerator = new PriorityQueue();
    }

    public function addContext(LinkGeneratorInterface $linkGenerator, int $priority = 0): void
    {
        $this->linkGenerator->insert($linkGenerator, $priority);
    }

    public function generate(object $object, array $params = []): string
    {
        foreach ($this->linkGenerator as $linkGenerator) {
            try {
                return $linkGenerator->generate($object, $params);
            } catch (LinkGenerationNotPossibleException) {
                continue;
            }
        }

        throw new LinkGenerationNotPossibleException();
    }
}
