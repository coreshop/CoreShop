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

use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

interface TaxRuleGroupInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param $name
     * @return static
     */
    public function setName($name);


    /**
     * @return bool
     */
    public function getActive();

    /**
     * @param bool $active
     * @return static
     */
    public function setActive($active);

     /**
     * @return Collection|TaxRuleInterface[]
     */
    public function getTaxRules();

    /**
     * @return bool
     */
    public function hasTaxRules();

    /**
     * @param TaxRuleInterface $taxRule
     */
    public function addTaxRule(TaxRuleInterface $taxRule);

    /**
     * @param TaxRuleInterface $taxRule
     */
    public function removeTaxRule(TaxRuleInterface $taxRule);

    /**
     * @param TaxRuleInterface $taxRule
     *
     * @return bool
     */
    public function hasTaxRule(TaxRuleInterface $taxRule);
}