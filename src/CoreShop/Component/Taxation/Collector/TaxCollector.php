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

namespace CoreShop\Component\Taxation\Collector;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use CoreShop\Component\Taxation\Model\TaxRateInterface;

class TaxCollector implements TaxCollectorInterface
{
    public function __construct(
        private RepositoryInterface $taxRateRepository,
        private FactoryInterface $taxItemFactory,
    ) {
    }

    public function collectTaxes(TaxCalculatorInterface $taxCalculator, int $price, array $usedTaxes = []): array
    {
        $taxesAmount = $taxCalculator->getTaxesAmountAsArray($price);

        foreach ($taxesAmount as $id => $amount) {
            $this->addTaxToArray($id, $amount, $usedTaxes);
        }

        return $usedTaxes;
    }

    public function collectTaxesFromGross(TaxCalculatorInterface $taxCalculator, int $price, array $usedTaxes = []): array
    {
        $taxesAmount = $taxCalculator->getTaxesAmountFromGrossAsArray($price);

        foreach ($taxesAmount as $id => $amount) {
            $this->addTaxToArray($id, $amount, $usedTaxes);
        }

        return $usedTaxes;
    }

    public function mergeTaxes(array $taxes1, array $taxes2): array
    {
        foreach ($taxes1 as $id => $tax) {
            $this->addTaxToArray($id, $tax->getAmount(), $taxes2);
        }

        return $taxes2;
    }

    private function addTaxToArray(int $taxId, int $amount, array &$usedTaxes): void
    {
        /**
         * @var TaxRateInterface|null $tax
         */
        $tax = $this->taxRateRepository->find($taxId);

        if ($amount === 0) {
            return;
        }

        if (!$tax instanceof TaxRateInterface) {
            return;
        }

        if (!array_key_exists($tax->getId(), $usedTaxes)) {
            /**
             * @var TaxItemInterface $item
             */
            $item = $this->taxItemFactory->createNew();
            $item->setName($tax->getName());
            $item->setRate($tax->getRate());
            $item->setTaxRate($tax);
            $item->setAmount($amount);

            $usedTaxes[$tax->getId()] = $item;
        } else {
            $usedTaxes[$tax->getId()]->setAmount($usedTaxes[$tax->getId()]->getAmount() + $amount);
        }
    }
}
