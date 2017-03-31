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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Taxation\Model;

use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Address\Model\StateInterface;
use CoreShop\Component\Core\Model\AbstractResource;

/**
 * Class TaxRule
 * @package CoreShop\Model
 */
class TaxRule extends AbstractResource implements TaxRuleInterface
{
    /**
     * @var int
     */
    public $taxRuleGroupId;

    /**
     * @var TaxRuleGroupInterface
     */
    public $taxRuleGroup;

    /**
     * @var int
     */
    public $countryId;

    /**
     * @var CountryInterface
     */
    public $country;

    /**
     * @var int
     */
    public $stateId;

    /**
     * @var StateInterface
     */
    public $state;

    /**
     * @var int
     */
    public $taxRateId;

    /**
     * @var TaxRateInterface
     */
    public $taxRate;

    /**
     * @var int
     */
    public $behavior;

    /**
     * @return string
     */
    public function __toString()
    {
        $country = $this->getCountry() instanceof CountryInterface ? $this->getCountry()->getName() : "none";
        $state = $this->getState() instanceof StateInterface ? $this->getState()->getName() : "none";
        $tax = $this->getTax() instanceof TaxRateInterface ? $this->getTax()->getName() : "none";

        return sprintf("%s (%s) (%s) (%s)", $tax, $country, $state, $this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getBehavior()
    {
        return $this->behavior;
    }

    /**
     * {@inheritdoc}
     */
    public function setBehavior($behavior)
    {
        $this->behavior = $behavior;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRuleGroupId()
    {
        return $this->taxRuleGroupId;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxRuleGroupId($taxRuleGroupId)
    {
        $this->taxRuleGroupId = $taxRuleGroupId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRuleGroup()
    {
        return $this->taxRuleGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxRuleGroup(TaxRuleGroupInterface $taxRuleGroup)
    {
        $this->taxRuleGroup = $taxRuleGroup;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * @return CountryInterface
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountry(CountryInterface $country)
    {
        $this->country = $country;

        return $this;
    }

   /**
     * {@inheritdoc}
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * {@inheritdoc}
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setState(StateInterface $state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRateId()
    {
        return $this->taxRateId;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxRateId($taxRateId)
    {
        $this->taxRateId = $taxRateId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }
}
