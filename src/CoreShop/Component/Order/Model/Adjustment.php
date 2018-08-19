<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreFieldcollection;

class Adjustment extends AbstractPimcoreFieldcollection implements AdjustmentInterface
{
    /**
     * @var int
     */
    protected $amount = 0;

    /**
     * @var bool
     */
    protected $neutral = false;

    /**
     * @var string|null
     */
    protected $originCode;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getObject()->getId().'_tax_item_'.$this->getIndex();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustable()
    {
        if ($this->getObject() instanceof AdjustableInterface) {
            return $this->getObject();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->getPimcoreAmount();
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount(int $amount)
    {
        $this->setPimcoreAmount($amount);

        if (!$this->getNeutral()) {
            $this->recalculateAdjustable();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNeutral()
    {
        return $this->getPimcoreNeutral();
    }

    /**
     * {@inheritdoc}
     */
    public function setNeutral(bool $neutral)
    {
        if ($this->getPimcoreNeutral() !== $neutral) {
            $this->setPimcoreNeutral($neutral);
            $this->recalculateAdjustable();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isCharge()
    {
        return 0 > $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredit()
    {
        return 0 < $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeIdentifier()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTypeIdentifier($typeIdentifier)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPimcoreAmount($pimcoreAmount)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPimcoreAmount()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPimcoreNeutral($pimcoreNeutral)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPimcoreNeutral()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginCode()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginCode($originCode)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     *
     */
    private function recalculateAdjustable()
    {
        $adjustable = $this->getAdjustable();
        if (null !== $adjustable) {
            $adjustable->recalculateAdjustmentsTotal();
        }
    }
}
