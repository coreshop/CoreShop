<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Taxation\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Store\Model\StoreInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class TaxRuleGroup extends AbstractResource implements TaxRuleGroupInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $active = true;

    /**
     * @var Collection|TaxRuleInterface[]
     */
    protected $taxRules;

    public function __construct()
    {
        $this->taxRules = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s (%s)', $this->getName(), $this->getId());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return static
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRules()
    {
        return $this->taxRules;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTaxRules()
    {
        return !$this->taxRules->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addTaxRule(TaxRuleInterface $taxRule)
    {
        if (!$this->hasTaxRule($taxRule)) {
            $this->taxRules->add($taxRule);

            $taxRule->setTaxRuleGroup($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeTaxRule(TaxRuleInterface $taxRule)
    {
        if ($this->hasTaxRule($taxRule)) {
            $this->taxRules->removeElement($taxRule);
            $taxRule->setTaxRuleGroup(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasTaxRule(TaxRuleInterface $taxRule)
    {
        return $this->taxRules->contains($taxRule);
    }
}
