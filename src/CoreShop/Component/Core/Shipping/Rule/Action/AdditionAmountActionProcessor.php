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

namespace CoreShop\Component\Core\Shipping\Rule\Action;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Model\CurrencyAwareInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Rule\Action\CarrierPriceActionProcessorInterface;
use CoreShop\Component\Shipping\Rule\Action\CarrierPriceModificationActionProcessorInterface;
use Webmozart\Assert\Assert;

class AdditionAmountActionProcessor implements CarrierPriceModificationActionProcessorInterface
{
    protected $moneyConverter;
    protected $currencyRepository;

    public function __construct(CurrencyRepositoryInterface $currencyRepository, CurrencyConverterInterface $moneyConverter)
    {
        $this->currencyRepository = $currencyRepository;
        $this->moneyConverter = $moneyConverter;
    }
    /**
     * {@inheritdoc}
     */
    public function getModification(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, int $price, array $configuration): int
    {
        $amount = $configuration['amount'];

        if ($shippable instanceof CurrencyAwareInterface) {
            $currency = $this->currencyRepository->find($configuration['currency']);

            Assert::isInstanceOf($currency, CurrencyInterface::class);

            return $this->moneyConverter->convert($amount, $currency->getIsoCode(), $shippable->getCurrency()->getIsoCode());
        }

        return $amount;
    }
}
