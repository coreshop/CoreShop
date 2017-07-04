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

use Carbon\Carbon;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Component\Order\Workflow\WorkflowManagerInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class QuoteController extends AbstractSaleController
{
    /**
     * {@inheritdoc}
     */
    public function getGridColumns()
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
}
