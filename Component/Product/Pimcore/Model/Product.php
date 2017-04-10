<?php

namespace CoreShop\Component\Product\Pimcore\Model;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

class Product extends AbstractPimcoreModel implements ProductInterface
{
    /**
     * @var TaxCalculatorInterface
     */
    private $taxCalculator;

    /**
     * {@inheritdoc}
     */
    public function getPrice($withTax = true)
    {
        /**
         * @var ProductPriceCalculatorInterface
         */
        $calculator = $this->getContainer()->get('coreshop.product.price_calculator');

        $netPrice = $calculator->getPrice($this);

        if ($withTax) {
            $taxCalculator = $this->getTaxCalculator();

            if ($taxCalculator instanceof TaxCalculatorInterface) {
                $netPrice = $taxCalculator->applyTaxes($netPrice);
            }
        }

        return $netPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function getRules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function hasRule(RuleInterface $rule)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function addRule(RuleInterface $rule)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function removeRule(RuleInterface $rule)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getBasePrice($withTax = true)
    {
        $netPrice = $this->getPimcoreBasePrice();

        if ($withTax) {
            $taxCalculator = $this->getTaxCalculator();

            if ($taxCalculator instanceof TaxCalculatorInterface) {
                $netPrice = $taxCalculator->applyTaxes($netPrice);
            }
        }

        return $netPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function setBasePrice($basePrice)
    {
        $this->setBasePrice($basePrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCalculator(AddressInterface $address = null)
    {
        if (is_null($this->taxCalculator)) {
            $factory = $this->getContainer()->get('coreshop.taxation.factory.tax_calculator');

            $taxRuleGroup = $this->getTaxRule();

            if ($taxRuleGroup instanceof TaxRuleGroupInterface) {
                $address = $this->getContainer()->get('coreshop.factory.address')->createNew();
                $country = $this->getContainer()->get('coreshop.context.country')->getCountry();

                $address->setCountry($country);

                $this->taxCalculator = $factory->getTaxCalculatorForAddress($taxRuleGroup, $address);
            }
            else {
                $this->taxCalculator = null;
            }
        }

        return $this->taxCalculator;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private function getContainer() {
        return \Pimcore::getContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function getName($language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name, $language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getWholesalePrice()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setWholesalePrice($wholesalePrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPimcoreBasePrice()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPimcoreBasePrice($basePrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableForOrder()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailableForOrder($availableForOrder)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRule()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxRule($taxRule)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
