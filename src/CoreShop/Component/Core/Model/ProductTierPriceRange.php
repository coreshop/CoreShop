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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\TierPricing\Model\ProductTierPriceRange as BaseProductTierPriceRange;

class ProductTierPriceRange extends BaseProductTierPriceRange implements ProductTierPriceRangeInterface
{
    /**
     * @var int
     */
    protected $amount;

    /**
     * @var CurrencyInterface|null
     */
    protected $currency;
    /***
     * @var int
     */
    protected $pseudoPrice;

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount(int $amount)
    {
        $this->amount = $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency(CurrencyInterface $currency = null)
    {
        $this->currency = $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getPseudoPrice()
    {
        return $this->pseudoPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPseudoPrice()
    {
        return $this->pseudoPrice !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function setPseudoPrice(int $pseudoPrice)
    {
        $this->pseudoPrice = $pseudoPrice;
    }
}
