<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\TierPricing\Model;

use Doctrine\Common\Collections\ArrayCollection;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;

interface ProductTierPriceInterface extends ResourceInterface, StoreAwareInterface
{
    /**
     * @return bool
     */
    public function getActive();

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @param boolean $active
     */
    public function setActive(bool $active);

    /**
     * @return string
     */
    public function getProperty();

    /**
     * @param string $property
     */
    public function setProperty(string $property);

    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);

    /**
     * @return ArrayCollection|ProductTierPriceRangeInterface[]
     */
    public function getRanges();

    /**
     * @return bool
     */
    public function hasRanges();

    /**
     * @param ProductTierPriceRangeInterface $range
     */
    public function addRange(ProductTierPriceRangeInterface $range);

    /**
     * @param ProductTierPriceRangeInterface $range
     */
    public function removeRange(ProductTierPriceRangeInterface $range);

    public function removeAllRanges();
}
