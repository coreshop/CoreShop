<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\OrderInvoiceStates;
use CoreShop\Component\Order\OrderShipmentStates;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180626083954 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        /** @var OrderShipmentRepositoryInterface $orderShipmentRepository */
        $orderShipmentRepository = $this->container->get('coreshop.repository.order_shipment');

        /** @var OrderInvoiceRepositoryInterface $orderInvoiceRepository */
        $orderInvoiceRepository = $this->container->get('coreshop.repository.order_invoice');

        $list = $this->container->get('coreshop.repository.order')->getList();
        $list->load();

        /**
         * @var $order OrderInterface
         */
        foreach ($list->getObjects() as $order) {
            $shipments = $orderShipmentRepository->getDocuments($order);
            $invoice = $orderInvoiceRepository->getDocuments($order);

            $changed = false;
            if (count($shipments) > 0 && $order->getShippingState() === OrderShipmentStates::STATE_NEW) {
                $changed = true;
                $order->setShippingState(OrderShipmentStates::STATE_READY);
            }

            if (count($invoice) > 0 && $order->getInvoiceState() === OrderInvoiceStates::STATE_NEW) {
                $changed = true;
                $order->setInvoiceState(OrderInvoiceStates::STATE_READY);
            }

            if ($changed === true) {
                $order->save();
            }
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
