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

namespace CoreShop\Component\Taxation\Collector;

use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use Doctrine\ORM\EntityRepository;

class TaxCollector implements TaxCollectorInterface
{
    /**
     * @var EntityRepository
     */
    private $taxRateRepository;

    /**
     * @param EntityRepository $taxRateRepository
     */
    public function __construct(EntityRepository $taxRateRepository)
    {
        $this->taxRateRepository = $taxRateRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function collectTaxes(TaxCalculatorInterface $taxCalculator, $price, $usedTaxes = [])
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
    public function mergeTaxes($taxes1, $taxes2)
    {
        foreach ($taxes1 as $id => $tax) {
            $this->addTaxToArray($id, $tax['amount'], $taxes2);
        }

        return $taxes2;
    }

    private function addTaxToArray($taxId, $amount, &$usedTaxes)
    {
        $tax = $this->taxRateRepository->find($taxId);

        if ($amount > 0 && $tax) {
            if (!array_key_exists($tax->getId(), $usedTaxes)) {
                $usedTaxes[$tax->getId()] = [
                    'tax' => $tax,
                    'amount' => $amount,
                ];
            } else {
                $usedTaxes[$tax->getId()]['amount'] += $amount;
            }
        }
    }
}
