<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\VariantBundle\Pimcore;

use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;

class VariantLinkGenerator implements LinkGeneratorInterface
{
    public function __construct(protected LinkGeneratorInterface $inner, protected bool $redirectToMainVariant = true)
    {
    }

    public function generate(Concrete $object, array $params = []): string
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