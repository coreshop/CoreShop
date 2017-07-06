<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Product\Model\Product as BaseProduct;
use CoreShop\Component\Resource\ImplementedByPimcoreException;

class Product extends BaseProduct implements ProductInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTaxCalculator(AddressInterface $address = null)
    {
        if (is_null($this->taxCalculator)) {
            $taxRuleGroup = $this->getTaxRule();

            if ($taxRuleGroup instanceof TaxRuleGroupInterface) {
                $factory = $this->getContainer()->get('coreshop.taxation.factory.tax_calculator');
                
                if (null === $address) {
                    $address = $this->getContainer()->get('coreshop.factory.address')->createNew();
                    $country = $this->getContainer()->get('coreshop.context.country')->getCountry();

                    $address->setCountry($country);
                }

                $this->taxCalculator = $factory->getTaxCalculatorForAddress($taxRuleGroup, $address);
            } else {
                $this->taxCalculator = null;
            }
        }

        return $this->taxCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getStores()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setStores($stores)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->getActive();
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexable()
    {
        return true;
    }
}