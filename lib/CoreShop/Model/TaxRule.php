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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

/**
 * Class TaxRule
 * @package CoreShop\Model
 */
class TaxRule extends AbstractModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $taxRuleGroupId;

    /**
     * @var TaxRuleGroup
     */
    public $taxRuleGroup;

    /**
     * @var int
     */
    public $countryId;

    /**
     * @var Country
     */
    public $country;

    /**
     * @var int
     */
    public $stateId;

    /**
     * @var State
     */
    public $state;

    /**
     * @var int
     */
    public $taxId;

    /**
     * @var Tax
     */
    public $tax;

    /**
     * @var int
     */
    public $behavior;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getBehavior()
    {
        return $this->behavior;
    }

    /**
     * @param int $behavior
     */
    public function setBehavior($behavior)
    {
        $this->behavior = $behavior;
    }

    /**
     * @return int
     */
    public function getTaxRuleGroupId()
    {
        return $this->taxRuleGroupId;
    }

    /**
     * @param int $taxRuleGroupId
     *
     * @throws \Exception
     */
    public function setTaxRuleGroupId($taxRuleGroupId)
    {
        $this->taxRuleGroupId = $taxRuleGroupId;
    }

    /**
     * @return TaxRuleGroup
     */
    public function getTaxRuleGroup()
    {
        if (!$this->taxRuleGroup instanceof TaxRuleGroup) {
            $this->taxRuleGroup = TaxRuleGroup::getById($this->taxRuleGroupId);
        }

        return $this->taxRuleGroup;
    }

    /**
     * @param int|TaxRuleGroup $taxRuleGroup
     *
     * @throws \Exception
     */
    public function setTaxRuleGroup($taxRuleGroup)
    {
        if (!$taxRuleGroup instanceof TaxRuleGroup) {
            throw new \Exception('$taxRuleGroup must be instance of TaxRuleGroup');
        }

        $this->taxRuleGroup = $taxRuleGroup;
        $this->taxRuleGroupId = $taxRuleGroup->getId();
    }

    /**
     * @return int
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     *
     * @throws \Exception
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        if (!$this->country instanceof Country) {
            $this->country = Country::getById($this->countryId);
        }

        return $this->country;
    }

    /**
     * @param int|Country $country
     *
     * @throws \Exception
     */
    public function setCountry($country)
    {
        if (!$country instanceof Country) {
            throw new \Exception('$country must be instance of Country');
        }

        $this->country = $country;
        $this->countryId = $country->getId();
    }

    /**
     * @return int
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * @param int $stateId
     *
     * @throws \Exception
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;
    }

    /**
     * @return State
     */
    public function getState()
    {
        if (!$this->state instanceof State) {
            $this->state = State::getById($this->stateId);
        }

        return $this->state;
    }

    /**
     * @param int|State $state
     *
     * @throws \Exception
     */
    public function setState($state)
    {
        if (!$state instanceof State) {
            throw new \Exception('$state must be instance of State');
        }

        $this->state = $state;
        $this->stateId = $state->getId();
    }

    /**
     * @return int
     */
    public function getTaxId()
    {
        return $this->taxId;
    }

    /**
     * @param int $taxId
     *
     * @throws \Exception
     */
    public function setTaxId($taxId)
    {
        $this->taxId = $taxId;
    }

    /**
     * @return Tax
     */
    public function getTax()
    {
        if (!$this->tax instanceof Tax) {
            $this->tax = Tax::getById($this->taxId);
        }

        return $this->tax;
    }

    /**
     * @param int|Tax $tax
     *
     * @throws \Exception
     */
    public function setTax($tax)
    {
        if (!$tax instanceof Tax) {
            throw new \Exception('$tax must be instance of Tax');
        }

        $this->tax = $tax;
        $this->taxId = $tax->getId();
    }
}
