<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Address\Context;

use Zend\Stdlib\PriorityQueue;

final class CompositeCountryContext implements CountryContextInterface
{
    /**
     * @var PriorityQueue|CountryContextInterface[]
     */
    private $countryContexts;

    public function __construct()
    {
        $this->countryContexts = new PriorityQueue();
    }

    /**
     * @param CountryContextInterface $countryContexts
     * @param int                     $priority
     */
    public function addContext(CountryContextInterface $countryContexts, $priority = 0)
    {
        $this->countryContexts->insert($countryContexts, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        foreach ($this->countryContexts as $countryContexts) {
            try {
                return $countryContexts->getCountry();
            } catch (CountryNotFoundException $exception) {
                continue;
            }
        }

        throw new CountryNotFoundException();
    }
}
