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

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AddToCartExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('coreshop_add_to_cart_available', [$this, 'isAddToCartAvailable']),
        ];
    }

    public function isAddToCartAvailable(PurchasableInterface $product): bool
    {
        if ($product instanceof ProductVariantAwareInterface) {
            if ($product->getType() === AbstractObject::OBJECT_TYPE_OBJECT && count($product->getAllowedAttributeGroups()) > 0) {
                return false;
            }
        }

        return true;
    }
}
