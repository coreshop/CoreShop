<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Resource\Pimcore;

use CoreShop\Component\Pimcore\Exception\LinkGenerationNotPossibleException;
use CoreShop\Component\Pimcore\Slug\SluggableInterface;
use CoreShop\Component\Resource\Metadata\RegistryInterface;
use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;

class ResourceSlugLinkGenerator implements LinkGeneratorInterface
{
    public function __construct(
        private LinkGeneratorInterface $inner,
        private RegistryInterface $metadataRegistry,
    ) {
    }

    public function generate(Concrete $object, array $params = []): string
    {
        if (!$object instanceof SluggableInterface) {
            throw new LinkGenerationNotPossibleException(
                sprintf(
                    'Object with Path "%s" must implement %s',
                    $object->getFullPath(),
                    SluggableInterface::class,
                ),
            );
        }

        if ($this->metadataRegistry->hasClass($object::class)) {
            $metadata = $this->metadataRegistry->getByClass($object::class);

            if ($metadata->hasParameter('slug') && !$metadata->getParameter('slug')) {
                throw new LinkGenerationNotPossibleException();
            }
        }

        return $this->inner->generate($object, $params);
    }
}
