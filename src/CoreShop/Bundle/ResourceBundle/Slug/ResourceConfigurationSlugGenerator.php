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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ResourceBundle\Slug;

use CoreShop\Component\Pimcore\Slug\DataObjectSlugGeneratorInterface;
use CoreShop\Component\Pimcore\Slug\SluggableInterface;
use CoreShop\Component\Resource\Metadata\RegistryInterface;

class ResourceConfigurationSlugGenerator implements DataObjectSlugGeneratorInterface
{
    public function __construct(
        private DataObjectSlugGeneratorInterface $inner,
        private RegistryInterface $metadataRegistry,
    ) {
    }

    public function generateSlugs(SluggableInterface $sluggable): void
    {
        if ($this->metadataRegistry->hasClass($sluggable::class)) {
            $metadata = $this->metadataRegistry->getByClass($sluggable::class);

            if ($metadata->hasParameter('slug') && !$metadata->getParameter('slug')) {
                return;
            }
        }

        $this->inner->generateSlugs($sluggable);
    }
}
