<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Taxation\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;

interface TaxRuleInterface extends ResourceInterface, TimestampableInterface
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
     * @param TaxRuleGroupInterface|null $taxRuleGroup
     */
    public function setTaxRuleGroup(TaxRuleGroupInterface $taxRuleGroup = null);

    /**
     * @return TaxRateInterface
     */
    public function getTaxRate();

    /**
     * @param TaxRateInterface $tax
     */
    public function setTaxRate(TaxRateInterface $tax);
}
