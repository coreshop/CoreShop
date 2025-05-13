<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Currency\Context;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class CachedCurrencyContext implements CurrencyContextInterface
{
    private ?CurrencyInterface $currency = null;

    public function __construct(
        private CurrencyContextInterface $inner,
        private RequestStack $requestStack,
    ) {
    }

    public function getCurrency(): CurrencyInterface
    {
        if ($this->requestStack->getMainRequest() instanceof Request) {
            if (null === $this->currency) {
                $this->currency = $this->inner->getCurrency();

                return $this->currency;
            }

            return $this->currency;
        }

        return $this->currency = $this->inner->getCurrency();
    }
}
