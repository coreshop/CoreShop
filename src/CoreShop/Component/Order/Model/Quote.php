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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;

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
}
