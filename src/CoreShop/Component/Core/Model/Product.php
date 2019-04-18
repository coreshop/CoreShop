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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Product\Model\Product as BaseProduct;
use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;

class Product extends BaseProduct implements ProductInterface
{
    /**
     * {@inheritdoc}
     */
    public function getInventoryName()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function isInStock()
    {
        return 0 < $this->getOnHand();
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle($language = null)
    {
        return $this->getPimcoreMetaTitle($language) ?: $this->getName($language);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription($language = null)
    {
        return $this->getPimcoreMetaDescription($language) ?: $this->getShortDescription($language);
    }

    /**
     * {@inheritdoc}
     */
    public function getOGTitle($language = null)
    {
        return $this->getMetaTitle($language);
    }

    /**
     * {@inheritdoc}
     */
    public function getOGDescription($language = null)
    {
        return $this->getMetaDescription($language);
    }

    /**
     * {@inheritdoc}
     */
    public function getOGType()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function getPimcoreMetaTitle($language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPimcoreMetaTitle($pimcoreMetaTitle, $language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPimcoreMetaDescription($language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPimcoreMetaDescription($pimcoreMetaDescription, $language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHold()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHold($onHold)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHand()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHand($onHand)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsTracked()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsTracked($tracked)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getMinimumQuantityToOrder()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setMinimumQuantityToOrder($minimumQuantity)
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
    public function getStoreValues(\CoreShop\Component\Store\Model\StoreInterface $store = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreValues($storeValues, \CoreShop\Component\Store\Model\StoreInterface $store = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreValuesOfType(string $type, \CoreShop\Component\Store\Model\StoreInterface $store)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreValuesOfType(string $type, $value, \CoreShop\Component\Store\Model\StoreInterface $store)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getDigitalProduct()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setDigitalProduct($digitalProduct)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantityPriceRules()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantityPriceRules($quantityPriceRules)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorePrice(\CoreShop\Component\Store\Model\StoreInterface $store = null)
    {
        if (null !== $store) {
            return $this->getStoreValuesOfType('price', $store);
        }

        $allStorePrices = [];
        /** @var ProductStoreValuesInterface $storeValuesBlock */
        foreach ($this->getStoreValues() as $storeValuesBlock) {
            $allStorePrices[$storeValuesBlock->getStore()->getId()] = $storeValuesBlock instanceof ProductStoreValuesInterface ? $storeValuesBlock->getPrice() : null;
        }

        return $allStorePrices;
    }

    /**
     * {@inheritdoc}
     */
    public function setStorePrice($storePrice, \CoreShop\Component\Store\Model\StoreInterface $store = null)
    {
        if (!is_int($storePrice) && !is_array($storePrice)) {
            throw new \InvalidArgumentException(sprintf('Expected value to either be an array or an int, "%s" given', gettype($storePrice)));
        }

        if (is_array($storePrice)) {
            foreach ($storePrice as $storeId => $singleStorePrice) {
                /** @var StoreRepositoryInterface $storeRepository */
                $storeRepository = \Pimcore::getContainer()->get('coreshop.repository.store');
                $currentStore = $storeRepository->find($storeId);

                $this->setStoreValuesOfType('price', $singleStorePrice, $currentStore);
            }

        } elseif ($store instanceof \CoreShop\Component\Store\Model\StoreInterface) {
            $this->setStoreValuesOfType('price', $storePrice, $store);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexableEnabled()
    {
        return $this->getActive() && $this->getPublished();
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexable()
    {
        return $this->getIndexableEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexableName($language)
    {
        return $this->getName($language);
    }
}
