<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Currency\Context;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class CachedCurrencyContext implements CurrencyContextInterface
{
    /**
     * @var CurrencyContextInterface
     */
    private $inner;

    /**
     * @var CurrencyInterface
     */
    private $currency;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param CurrencyContextInterface $inner
     * @param RequestStack             $requestStack
     */
    public function __construct(CurrencyContextInterface $inner, RequestStack $requestStack)
    {
        $this->inner = $inner;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
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
