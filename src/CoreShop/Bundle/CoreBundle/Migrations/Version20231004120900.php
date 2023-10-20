<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Pimcore\BatchProcessing\BatchListing;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20231004120900 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $listing = $this->container->get('coreshop.repository.order')->getList();
        $listing->setCondition('saleState = \'order\'');

        $batchListing = new BatchListing($listing, 50);

        /**
         * @var OrderInterface $order
         */
        foreach ($batchListing as $order) {
            $order->setImmutable(true);

            foreach ($order->getItems() as $item) {
                $item->setImmutable(true);
                $item->save();
            }

            $order->save();
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
