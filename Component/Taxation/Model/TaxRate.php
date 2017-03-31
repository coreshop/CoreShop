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

use CoreShop\Component\Core\Model\AbstractResource;
use CoreShop\Component\Core\Model\LocalizableTrait;

class TaxRate extends AbstractResource implements TaxRateInterface
{
    use LocalizableTrait {
        LocalizableTrait::__construct as private LocalizableTraitConstruct;
    }

    /**
     * @var float
     */
    public $rate;

    /**
     * @var bool
     */
    public $active;

    /**
     * TaxRate constructor.
     */
    public function __construct()
    {
        $this->LocalizableTraitConstruct(['name']);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s (%s)", $this->getName(), $this->getId());
    }

    /**
     * @param string $language language
     *
     * @return string
     */
    public function getName($language = null)
    {
        return $this->getLocalizedFields()->getLocalizedValue('name', $language);
    }

    /**
     * @param string $name
     * @param string $language language
     * @return static
     */
    public function setName($name, $language = null)
    {
        $this->getLocalizedFields()->setLocalizedValue('name', $name, $language);

        return $this;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param float $rate
     * @return static
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

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
     * @return static
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }
}
