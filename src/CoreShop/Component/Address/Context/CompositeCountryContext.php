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

declare(strict_types=1);

namespace CoreShop\Component\Address\Context;

use CoreShop\Component\Address\Model\CountryInterface;
use Laminas\Stdlib\PriorityQueue;

final class CompositeCountryContext implements CountryContextInterface
{
    /**
     * @var PriorityQueue|CountryContextInterface[]
     */
    private PriorityQueue $countryContexts;

    public function __construct()
    {
        $this->countryContexts = new PriorityQueue();
    }

    public function addContext(CountryContextInterface $countryContexts, $priority = 0): void
    {
        $this->countryContexts->insert($countryContexts, $priority);
    }

    public function getCountry(): CountryInterface
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
