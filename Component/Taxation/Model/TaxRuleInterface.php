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

use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Address\Model\StateInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

interface TaxRuleInterface extends ResourceInterface
{
    /**
     * @return int
     */
    public function getBehavior();
    /**
     * @param int $behavior
     */
    public function setBehavior($behavior);

    /**
     * @return int
     */
    public function getTaxRuleGroupId();

    /**
     * @param int $taxRuleGroupId
     */
    public function setTaxRuleGroupId($taxRuleGroupId);

    /**
     * @return TaxRuleGroupInterface
     */
    public function getTaxRuleGroup();

    /**
     * @param TaxRuleGroupInterface $taxRuleGroup
     */
    public function setTaxRuleGroup(TaxRuleGroupInterface $taxRuleGroup);

    /**
     * @return int
     */
    public function getCountryId();

    /**
     * @param int $countryId
     */
    public function setCountryId($countryId);

    /**
     * @return CountryInterface
     */
    public function getCountry();

    /**
     * @param CountryInterface $country
     */
    public function setCountry(CountryInterface $country);

    /**
     * @return int
     */
    public function getStateId();

    /**
     * @param int $stateId
     */
    public function setStateId($stateId);

    /**
     * @return StateInterface
     */
    public function getState();

    /**
     * @param StateInterface $state
     */
    public function setState(StateInterface $state);

    /**
     * @return int
     */
    public function getTaxRateId();

    /**
     * @param int $taxId
     */
    public function setTaxRateId($taxId);

    /**
     * @return TaxRateInterface
     */
    public function getTaxRate();

    /**
     * @param TaxRateInterface $tax
     */
    public function setTaxRate($tax);
}