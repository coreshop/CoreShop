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

namespace CoreShop\Component\Order\Calculator;

use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Order\Exception\NoPurchasableWholesalePriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Webmozart\Assert\Assert;

class PurchasableWholesalePriceCalculator implements PurchasableWholesalePriceCalculatorInterface
{
    public function __construct(private CurrencyConverterInterface $currencyConverter)
    {
    }

    public function getPurchasableWholesalePrice(PurchasableInterface $purchasable, array $context): int
    {
        Assert::keyExists($context, 'currency');
        Assert::isInstanceOf($context['currency'], CurrencyInterface::class);

        /**
         * @var CurrencyInterface $contextCurrency
         */
        $contextCurrency = $context['currency'];

        $wholesalePrice = $purchasable->getWholesaleBuyingPrice();

        if (!$wholesalePrice) {
            throw new NoPurchasableWholesalePriceFoundException(__CLASS__);
        }

        if (!$wholesalePrice->getCurrency()) {
            throw new NoPurchasableWholesalePriceFoundException(__CLASS__);
        }

        return $this->currencyConverter->convert($wholesalePrice->getValue(), $wholesalePrice->getCurrency()->getIsoCode(), $contextCurrency->getIsoCode());
    }
}
