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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroup as BaseTaxRuleGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class TaxRuleGroup extends BaseTaxRuleGroup implements TaxRuleGroupInterface
{
    /**
     * @var Collection|StoreInterface[]
     */
    protected $stores;

    public function __construct()
    {
        parent::__construct();

        $this->stores = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * {@inheritdoc}
     */
    public function hasStores()
    {
        return !$this->stores->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addStore(StoreInterface $store)
    {
        if (!$this->hasStore($store)) {
            $this->stores->add($store);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeStore(StoreInterface $store)
    {
        if ($this->hasStore($store)) {
            $this->stores->removeElement($store);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasStore(StoreInterface $store)
    {
        return $this->stores->contains($store);
    }
}
