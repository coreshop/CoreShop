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

namespace CoreShop\Component\Address\Context\RequestBased;

use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Address\Model\CountryInterface;
use Symfony\Component\HttpFoundation\Request;
use Zend\Stdlib\PriorityQueue;

final class CompositeRequestResolver implements RequestResolverInterface
{
    /**
     * @var PriorityQueue|RequestResolverInterface[]
     */
    private $requestResolvers;

    public function __construct()
    {
        $this->requestResolvers = new PriorityQueue();
    }

    public function addResolver(RequestResolverInterface $requestResolver, int $priority = 0): void
    {
        $this->requestResolvers->insert($requestResolver, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function findCountry(Request $request): CountryInterface
    {
        foreach ($this->requestResolvers as $requestResolver) {
            try {
                return $requestResolver->findCountry($request);
            }
            catch (CountryNotFoundException $ex)
            {
                //Silently ignore and continue
            }
        }

        throw new CountryNotFoundException();
    }
}
