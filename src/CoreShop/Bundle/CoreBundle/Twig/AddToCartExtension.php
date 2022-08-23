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
