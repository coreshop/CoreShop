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

namespace CoreShop\Bundle\OrderBundle\Controller;




class QuoteController extends AbstractSaleDetailController
{
    /**
     * {@inheritdoc}
     */
    protected function getGridColumns()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getSaleRepository()
    {
        return $this->get('coreshop.repository.quote');
    }

    /**
     * {@inheritdoc}
     */
    protected function getSalesList()
    {
        return $this->getSaleRepository()->getList();
    }

    /**
     * {@inheritdoc}
     */
    protected function getSaleClassName()
    {
        return 'coreshop.model.quote.pimcore_class_id';
    }

    /**
     * {@inheritdoc}
     */
    protected function getOrderKey()
    {
        return 'quoteDate';
    }

    /**
     * {@inheritdoc}
     */
    protected function getSaleNumberField()
    {
        return 'quoteNumber';
    }
}
