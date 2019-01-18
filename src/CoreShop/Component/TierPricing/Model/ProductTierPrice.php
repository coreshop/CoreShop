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

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Store\Model\StoreAwareTrait;
use Doctrine\Common\Collections\ArrayCollection;

class ProductTierPrice extends AbstractResource implements ProductTierPriceInterface
{
    use StoreAwareTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var string
     */
    protected $property;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var ArrayCollection|ProductTierPriceRangeInterface[]
     */
    protected $ranges;

    public function __construct()
    {
        $this->ranges = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->active === true;
    }

    /**
     * {@inheritdoc}
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * {@inheritdoc}
     */
    public function setProperty(string $property)
    {
        $this->property = $property;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function getRanges()
    {
        return $this->ranges;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRanges()
    {
        return !$this->ranges->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addRange(ProductTierPriceRangeInterface $range)
    {
        if (!$this->ranges->contains($range)) {
            $this->ranges->add($range);
            $range->setTierPrice($this);
        }
    }

    public function removeRange(ProductTierPriceRangeInterface $range)
    {
        if ($this->ranges->contains($range)) {
            $this->ranges->removeElement($range);
            $range->setTierPrice(null);
        }
    }

    public function removeAllRanges()
    {
        $this->ranges->clear();
    }
}
