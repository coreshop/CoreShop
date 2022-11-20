<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Pimcore\BatchProcessing\DataObjectBatchListing;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\ValidationException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20221118072640 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $listing = $this->container->get('coreshop.repository.order_item')->getList();
        $batchListing = new DataObjectBatchListing($listing, 100);

        foreach ($batchListing as $orderItem) {
            if (!$orderItem instanceof OrderItemInterface) {
                continue;
            }

            $order = $this->findOrder($orderItem);

            if ($order instanceof OrderInterface) {
                if ($orderItem instanceof Concrete) {
                    $orderItem->setOmitMandatoryCheck(true);
                }

                $orderItem->setOrder($order);

                try {
                    $orderItem->save();
                }
                catch (ValidationException $exception) {
                    $this->write(sprintf('Failed migrating OrderItem "%s" with error "%s"', $orderItem->getId(), $exception->getMessage()));
                }
            }
        }
    }

    private function findOrder(OrderItemInterface $orderItem): ?OrderInterface
    {
        $parent = $orderItem->getParent();
        do {
            if ($parent instanceof OrderInterface) {
                return $parent;
            }
            $parent = $parent->getParent();
        } while ($parent !== null);

        return null;
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
