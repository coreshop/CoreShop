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

declare(strict_types=1);

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Model\ProductInterface as BaseProductInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\SEO\Model\PimcoreSEOAwareInterface;
use CoreShop\Component\SEO\Model\SEOImageAwareInterface;
use CoreShop\Component\SEO\Model\SEOOpenGraphAwareInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;

interface ProductInterface extends
    BaseProductInterface,
    IndexableInterface,
    PurchasableInterface,
    StockableInterface,
    PimcoreSEOAwareInterface,
    SEOImageAwareInterface,
    SEOOpenGraphAwareInterface,
    QuantityRangePriceAwareInterface,
    PimcoreStoresAwareInterface
{
    /**
     * @return \CoreShop\Component\Core\Model\ProductStoreValuesInterface[]
     */
    public function getStoreValues (): array;

    public function setStoreValues (array $storeValues): self;

    public function getStoreValuesForStore (\CoreShop\Component\Store\Model\StoreInterface $store): ?\CoreShop\Component\Core\Model\ProductStoreValuesInterface;

    public function setStoreValuesForStore(ProductStoreValuesInterface $storeValues, \CoreShop\Component\Store\Model\StoreInterface $store): self;

    public function getStoreValuesOfType(string $type, \CoreShop\Component\Store\Model\StoreInterface $store);

    public function setStoreValuesOfType(string $type, $value, \CoreShop\Component\Store\Model\StoreInterface $store): self;

    public function setTaxRule(?TaxRuleGroupInterface $taxRule);

    public function getDigitalProduct(): ?bool;

    public function setDigitalProduct(?bool $digitalProduct);

    public function getMinimumQuantityToOrder(): ?int;

    public function setMinimumQuantityToOrder(?int $minimumQuantity);

    public function getMaximumQuantityToOrder(): ?int;

    public function setMaximumQuantityToOrder(?int $maximumQuantity);
}
