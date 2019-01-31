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

namespace CoreShop\Component\Core\TierPricing\Rule\Action;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;
use CoreShop\Component\TierPricing\Model\TierPriceAwareInterface;
use CoreShop\Component\TierPricing\Rule\Action\TierPriceActionInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Webmozart\Assert\Assert;

class AmountIncreaseAction implements TierPriceActionInterface
{
    /**
     * @var CurrencyConverterInterface
     */
    private $currencyConverter;

    /**
     * @param CurrencyConverterInterface $currencyConverter
     */
    public function __construct(CurrencyConverterInterface $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(ProductTierPriceRangeInterface $range, TierPriceAwareInterface $subject, int $realItemPrice, array $context)
    {
        /**
         * @var \CoreShop\Component\Core\Model\ProductTierPriceRangeInterface $range
         */
        Assert::isInstanceOf($range, \CoreShop\Component\Core\Model\ProductTierPriceRangeInterface::class);
        Assert::isInstanceOf($range->getCurrency(), CurrencyInterface::class);
        $currentContextCurrency = $context['currency'];
        $currencyAwareAmount = $this->currencyConverter->convert($range->getAmount(), $range->getCurrency()->getIsoCode(), $currentContextCurrency->getIsoCode());

        return $realItemPrice + $currencyAwareAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchFormValidation(FormInterface $form, ProductTierPriceRangeInterface $range)
    {
        /**
         * @var \CoreShop\Component\Core\Model\ProductTierPriceRangeInterface $range
         */
        Assert::isInstanceOf($range, \CoreShop\Component\Core\Model\ProductTierPriceRangeInterface::class);

        if (!$range->getCurrency() instanceof CurrencyInterface) {
            $form->addError(new FormError('no currency selected'));
        }
    }
}
