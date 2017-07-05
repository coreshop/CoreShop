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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Product\Helper\VariantHelper;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Model\ToggleableTrait;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;

class Product extends AbstractPimcoreModel implements ProductInterface
{
    use ToggleableTrait;

    /**
     * @var TaxCalculatorInterface
     */
    private $taxCalculator;

    /**
     * {@inheritdoc}
     */
    public function getPrice($withTax = true)
    {
        $variable = 'price'.($withTax ? 'Gross' : 'Net');

        /**
         * @var ProductPriceCalculatorInterface
         */
        $calculator = $this->getContainer()->get('coreshop.product.price_calculator');

        $netPrice = $calculator->getPrice($this);
        $discount = $calculator->getDiscount($this, $netPrice);
        $price = $netPrice - $discount;

        if ($price < 0) {
            $price = 0;
        }

        if ($withTax) {
            $taxCalculator = $this->getTaxCalculator();

            if ($taxCalculator instanceof TaxCalculatorInterface) {
                $price = $taxCalculator->applyTaxes($price);
            }
        }

        $this->$variable = $price;

        return $this->$variable;
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
        $this->setPimcoreBasePrice($basePrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRate()
    {
        $calculator = $this->getTaxCalculator();

        if ($calculator) {
            return $calculator->getTotalRate();
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxAmount()
    {
        $calculator = $this->getTaxCalculator();

        if ($calculator) {
            return $calculator->getTaxesAmount($this->getPrice(false));
        }

        return 0.0;
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
            } else {
                $this->taxCalculator = null;
            }
        }

        return $this->taxCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        if (count($this->getImages()) > 0) {
            return $this->getImages()[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariantDifferences($language, $type = 'objectbricks', $field = 'variants')
    {
        $master = $this->getVariantMaster();

        if ($master instanceof self) {
            $differences = VariantHelper::getProductVariations($master, $this, $type, $field, $language);

            return $differences;
        }

        return false;
    }

    /**
     * Return Topmost Master if Object is Variant.
     *
     * @return PimcoreModelInterface
     */
    protected function getVariantMaster()
    {
        $master = $this;

        while ($master->getType() === 'variant') {
            $master = $master->getParent();
        }

        return $master;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private function getContainer()
    {
        return \Pimcore::getContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getActive()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setActive($active)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
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
    public function getSpecificPriceRules()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setSpecificPriceRules($specificPriceRules)
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

    /**
     * {@inheritdoc}
     */
    public function getCategories()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategories($categories)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getImages()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setImages($images)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getManufacturer()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setManufacturer($manufacturer)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getEan()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setEan($ean)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsAvailableWhenOutOfStock()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsAvailableWhenOutOfStock($isAvailableWhenOutOfStock)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getShortDescription($language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShortDescription($shortDescription, $language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description, $language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setWeight($weight)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setWidth($width)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setHeight($height)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getDepth()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setDepth($depth)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
