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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\ImplementedByPimcoreException;

class Quote extends Sale implements QuoteInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSaleDate()
    {
        return $this->getQuoteDate();
    }

    /**
     * {@inheritdoc}
     */
    public function setSaleDate($saleDate)
    {
        return $this->setQuoteDate($saleDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getSaleNumber()
    {
        return $this->getQuoteNumber();
    }

    /**
     * {@inheritdoc}
     */
    public function setSaleNumber($saleNumber)
    {
        return $this->setQuoteNumber($saleNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteDate()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteDate($quoteDate)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteNumber()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteNumber($quoteNumber)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getSaleLanguage()
    {
        @trigger_error(sprintf('The %s() method is deprecated since CoreShop 2.0.0-beta.1 and will be removed in CoreShop 2.0.0-beta.2. Please us getLocaleCode instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->getLocaleCode();
    }

    /**
     * {@inheritdoc}
     */
    public function setSaleLanguage($saleLanguage)
    {
        @trigger_error(sprintf('The %s() method is deprecated since CoreShop 2.0.0-beta.1 and will be removed in CoreShop 2.0.0-beta.2. Please us setLocaleCode instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->setLocaleCode($saleLanguage);
    }


    /**
     * {@inheritdoc}
     */
    public function getQuoteLanguage()
    {
        @trigger_error(sprintf('The %s() method is deprecated since CoreShop 2.0.0-beta.1 and will be removed in CoreShop 2.0.0-beta.2. Please us getLocaleCode instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->getLocaleCode();
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteLanguage($quoteLanguage)
    {
        @trigger_error(sprintf('The %s() method is deprecated since CoreShop 2.0.0-beta.1 and will be removed in CoreShop 2.0.0-beta.2. Please us setLocaleCode instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->setLocaleCode($quoteLanguage);
    }
}
