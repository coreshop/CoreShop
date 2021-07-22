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

namespace CoreShop\Component\Currency\Context;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class CachedCurrencyContext implements CurrencyContextInterface
{
    private CurrencyContextInterface $inner;
    private RequestStack $requestStack;
    private ?CurrencyInterface $currency = null;

    public function __construct(CurrencyContextInterface $inner, RequestStack $requestStack)
    {
        $this->inner = $inner;
        $this->requestStack = $requestStack;
    }

    public function getCurrency(): CurrencyInterface
    {
        if ($this->requestStack->getMasterRequest() instanceof Request) {
            if (null === $this->currency) {
                $this->currency = $this->inner->getCurrency();

                return $this->currency;
            }

            return $this->currency;
        }

        return $this->currency = $this->inner->getCurrency();
    }
}
