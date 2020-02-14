<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Templating\Helper;

use CoreShop\Component\Order\Model\PurchasableInterface;
use Symfony\Component\Templating\Helper\HelperInterface;

interface ProductPriceHelperInterface extends HelperInterface
{
    public function getPrice(PurchasableInterface $product, bool $withTax = true, array $context = []): int;

    public function getRetailPrice(PurchasableInterface $product, bool $withTax = true, array $context = []): int;

    public function getDiscountPrice(PurchasableInterface $product, bool $withTax = true, array $context = []): int;

    public function getDiscount(PurchasableInterface $product, bool $withTax = true, array $context = []): int;
}
