<?php

namespace CoreShop\Component\Taxation\Collector;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

class TaxCollector implements TaxCollectorInterface {
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
    public function collectTaxes(TaxCalculatorInterface $taxCalculator, $price, $usedTaxes = []) {
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
    public function mergeTaxes($taxes1, $taxes2) {
        foreach ($taxes1 as $id => $tax) {
            $this->addTaxToArray($id, $tax['amount'], $taxes2);
        }

        return $taxes2;
    }

    private function addTaxToArray($taxId, $amount, &$usedTaxes) {
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