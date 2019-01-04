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

namespace CoreShop\Component\Product\Rule\Action;

use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use Webmozart\Assert\Assert;

class DiscountAmountActionProcessor implements ProductDiscountActionProcessorInterface
{
    /**
     * @var CurrencyConverterInterface
     */
    protected $moneyConverter;

    /**
     * @var CurrencyRepositoryInterface
     */
    protected $currencyRepository;

    /**
     * @param CurrencyRepositoryInterface $currencyRepository
     * @param CurrencyConverterInterface  $moneyConverter
     */
    public function __construct(CurrencyRepositoryInterface $currencyRepository, CurrencyConverterInterface $moneyConverter)
    {
        $this->currencyRepository = $currencyRepository;
        $this->moneyConverter = $moneyConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount($subject, $price, array $context, array $configuration)
    {
        Assert::keyExists($context, 'currency');
        Assert::isInstanceOf($context['currency'], CurrencyInterface::class);

        /**
         * @var CurrencyInterface $currency
         * @var CurrencyInterface $contextCurrency
         */
        $contextCurrency = $context['currency'];
        $amount = $configuration['amount'];
        $currency = $this->currencyRepository->find($configuration['currency']);

        Assert::isInstanceOf($currency, CurrencyInterface::class);

        return $this->moneyConverter->convert($amount, $currency->getIsoCode(), $contextCurrency->getIsoCode());
    }
}
