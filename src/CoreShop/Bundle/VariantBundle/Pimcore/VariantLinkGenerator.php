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

namespace CoreShop\Bundle\VariantBundle\Pimcore;

use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;

class VariantLinkGenerator implements LinkGeneratorInterface
{
    public function __construct(
        protected LinkGeneratorInterface $inner,
        protected bool $redirectToMainVariant = true,
    ) {
    }

    public function generate(object $object, array $params = []): string
    {
        if (!$object instanceof ProductVariantAwareInterface) {
            return $this->inner->generate($object, $params);
        }

        if ($object->getType() === Concrete::OBJECT_TYPE_VARIANT) {
            return $this->inner->generate($object, $params);
        }

        $mainVariant = $object->getMainVariant();

        if (!$mainVariant instanceof Concrete || !$this->redirectToMainVariant) {
            return $this->inner->generate($object, $params);
        }

        return $this->inner->generate($mainVariant, $params);
    }
}
