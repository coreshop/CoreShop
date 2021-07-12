<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Taxation\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;

class TaxRule extends AbstractResource implements TaxRuleInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var TaxRuleGroupInterface
     */
    protected $taxRuleGroup;

    /**
     * @var TaxRateInterface
     */
    protected $taxRate;

    /**
     * @var int
     */
    protected $behavior;

    /**
     * @return string
     */
    public function __toString()
    {
        $tax = $this->getTaxRate() instanceof TaxRateInterface ? $this->getTaxRate()->getName() : 'none';

        return sprintf('%s (%s)', $tax, $this->getId());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getBehavior()
    {
        return $this->behavior;
    }

    public function setBehavior($behavior)
    {
        $this->behavior = $behavior;

        return $this;
    }

    public function getTaxRuleGroup()
    {
        return $this->taxRuleGroup;
    }

    public function setTaxRuleGroup(TaxRuleGroupInterface $taxRuleGroup = null)
    {
        $this->taxRuleGroup = $taxRuleGroup;

        return $this;
    }

    public function getTaxRate()
    {
        return $this->taxRate;
    }

    public function setTaxRate(TaxRateInterface $taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }
}
