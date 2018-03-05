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

namespace CoreShop\Component\Shipping\Rule\Action;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

class AdditionAmountActionProcessor implements CarrierPriceActionProcessorInterface
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
     * @var CurrencyContextInterface
     */
    protected $currencyContext;

    /**
     * @param CurrencyRepositoryInterface $currencyRepository
     * @param CurrencyConverterInterface $moneyConverter
     * @param CurrencyContextInterface $currencyContext
     */
    public function __construct(CurrencyRepositoryInterface $currencyRepository, CurrencyConverterInterface $moneyConverter, CurrencyContextInterface $currencyContext)
    {
        $this->currencyRepository = $currencyRepository;
        $this->moneyConverter = $moneyConverter;
        $this->currencyContext = $currencyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, array $configuration)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getModification(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, $price, array $configuration)
    {
        $amount = $configuration['amount'];
        $currency = $this->currencyRepository->find($configuration['currency']);

        return $this->moneyConverter->convert($amount, $currency->getIsoCode(), $this->currencyContext->getCurrency()->getIsoCode());
    }
}
