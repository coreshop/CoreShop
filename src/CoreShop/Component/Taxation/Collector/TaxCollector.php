<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Taxation\Collector;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use CoreShop\Component\Taxation\Model\TaxRateInterface;

class TaxCollector implements TaxCollectorInterface
{
    /**
     * @var RepositoryInterface
     */
    private $taxRateRepository;

    /**
     * @var FactoryInterface
     */
    private $taxItemFactory;

    /**
     * @param RepositoryInterface $taxRateRepository
     * @param FactoryInterface    $taxItemFactory
     */
    public function __construct(
        RepositoryInterface $taxRateRepository,
        FactoryInterface $taxItemFactory
    ) {
        $this->taxRateRepository = $taxRateRepository;
        $this->taxItemFactory = $taxItemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function collectTaxes(TaxCalculatorInterface $taxCalculator, $price, array $usedTaxes = [])
    {
        if ($taxCalculator instanceof TaxCalculatorInterface) {
            $taxesAmount = $taxCalculator->getTaxesAmount($price, true);

            if (is_array($taxesAmount)) {
                foreach ($taxesAmount as $id => $amount) {
                    $this->addTaxToArray($id, $amount, $usedTaxes);
                }
            }
        }

        return $usedTaxes;
    }

    /**
     * {@inheritdoc}
     */
    public function collectTaxesFromGross(TaxCalculatorInterface $taxCalculator, $price, array $usedTaxes = [])
    {
        if ($taxCalculator instanceof TaxCalculatorInterface) {
            $taxesAmount = $taxCalculator->getTaxesAmountFromGross($price, true);

            if (is_array($taxesAmount)) {
                foreach ($taxesAmount as $id => $amount) {
                    $this->addTaxToArray($id, $amount, $usedTaxes);
                }
            }
        }

        return $usedTaxes;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeTaxes(array $taxes1, array $taxes2)
    {
        foreach ($taxes1 as $id => $tax) {
            $this->addTaxToArray($id, $tax->getAmount(), $taxes2);
        }

        return $taxes2;
    }

    /**
     * @param int   $taxId
     * @param int   $amount
     * @param array $usedTaxes
     */
    private function addTaxToArray($taxId, $amount, &$usedTaxes)
    {
        /**
         * @var TaxRateInterface $tax
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
            $item->setAmount($amount);

            $usedTaxes[$tax->getId()] = $item;
        } else {
            $usedTaxes[$tax->getId()]->setAmount($usedTaxes[$tax->getId()]->getAmount() + $amount);
        }
    }
}
