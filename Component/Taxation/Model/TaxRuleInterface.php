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
     * @return TaxRuleGroupInterface
     */
    public function getTaxRuleGroup();

    /**
     * @param TaxRuleGroupInterface $taxRuleGroup
     */
    public function setTaxRuleGroup(TaxRuleGroupInterface $taxRuleGroup);

    /**
     * @return TaxRateInterface
     */
    public function getTaxRate();

    /**
     * @param TaxRateInterface $tax
     */
    public function setTaxRate(TaxRateInterface $tax);
}
