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

namespace CoreShop\Component\Address\Context\RequestBased;

use CoreShop\Component\Address\Context\RequestBased\RequestResolverInterface;
use CoreShop\Component\Address\Model\CountryInterface;
use Symfony\Component\HttpFoundation\Request;

final class CachedCountryContext implements RequestResolverInterface
{
    /**
     * @var RequestResolverInterface
     */
    private $inner;

    /**
     * @var CountryInterface
     */
    private $country;

    /**
     * @param RequestResolverInterface $inner
     */
    public function __construct(RequestResolverInterface $inner)
    {
        $this->inner = $inner;
    }

    /**
     * {@inheritdoc}
     */
    public function findCountry(Request $request): CountryInterface
    {
        if (null === $this->country) {
            $this->country = $this->inner->findCountry($request);
        }

        return $this->country;
    }
}
