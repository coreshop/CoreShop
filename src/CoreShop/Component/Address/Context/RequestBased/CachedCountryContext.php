<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Address\Context\RequestBased;

use CoreShop\Component\Address\Model\CountryInterface;
use Symfony\Component\HttpFoundation\Request;

final class CachedCountryContext implements RequestResolverInterface
{
    private ?CountryInterface $country = null;

    public function __construct(
        private RequestResolverInterface $inner,
    ) {
    }

    public function findCountry(Request $request): CountryInterface
    {
        if (null === $this->country) {
            $this->country = $this->inner->findCountry($request);
        }

        return $this->country;
    }
}
