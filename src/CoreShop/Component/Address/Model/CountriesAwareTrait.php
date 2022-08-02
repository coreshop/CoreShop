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

namespace CoreShop\Component\Address\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait CountriesAwareTrait
{
    /**
     * @var Collection|CountryInterface[]
     */
    protected $countries;

    public function __construct()
    {
        $this->countries = new ArrayCollection();
    }

    public function getCountries()
    {
        return $this->countries;
    }

    public function hasCountries()
    {
        return !$this->countries->isEmpty();
    }

    public function addCountry(CountryInterface $country)
    {
        if (!$this->hasCountry($country)) {
            $this->countries->add($country);
        }
    }

    public function removeCountry(CountryInterface $country)
    {
        if ($this->hasCountry($country)) {
            $this->countries->removeElement($country);
        }
    }

    public function hasCountry(CountryInterface $country)
    {
        return $this->countries->contains($country);
    }
}
