<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Address\Model\StateInterface;
use CoreShop\Component\Taxation\Model\TaxRateInterface;
use CoreShop\Component\Taxation\Model\TaxRule as BaseTaxRule;

class TaxRule extends BaseTaxRule implements TaxRuleInterface
{
    /**
     * @var CountryInterface
     */
    protected $country;

    /**
     * @var StateInterface
     */
    protected $state;

    /**
     * @return string
     */
    public function __toString()
    {
        $country = $this->getCountry() instanceof CountryInterface ? $this->getCountry()->getName() : 'none';
        $state = $this->getState() instanceof StateInterface ? $this->getState()->getName() : 'none';
        $tax = $this->getTaxRate() instanceof TaxRateInterface ? $this->getTaxRate()->getName() : 'none';

        return sprintf('%s (%s) (%s) (%s)', $tax, $country, $state, $this->getId());
    }

    /**
     * @return CountryInterface
     */
    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry(CountryInterface $country = null)
    {
        $this->country = $country;

        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState(StateInterface $state = null)
    {
        $this->state = $state;

        return $this;
    }
}
