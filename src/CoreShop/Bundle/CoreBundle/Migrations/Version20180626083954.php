<?php

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
