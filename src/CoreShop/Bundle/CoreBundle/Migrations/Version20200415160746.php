<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderInvoiceItemInterface;
use CoreShop\Component\Pimcore\BatchProcessing\BatchListing;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200415160746 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->writeMessage('Start migration for Order Invoice Item Objects');

        $orderInvoiceItemList = $this->container->get('coreshop.repository.order_invoice_item')->getList();
        $batchList = new BatchListing($orderInvoiceItemList, 50);

        $fieldMap = [
            'totalNet' => 'convertedTotalNet',
            'totalGross' => 'convertedTotalGross',

            'baseTotalNet' => 'totalNet',
            'baseTotalGross' => 'totalGross',
        ];

        $fieldsNotMigrated = [];

        foreach ($batchList as $orderInvoiceItem) {
            if (!$orderInvoiceItem instanceof OrderInvoiceItemInterface) {
                continue;
            }

            foreach ($fieldMap as $from => $to) {
                $getterFrom = 'get' . ucfirst($from);
                $setterTo = 'set' . ucfirst($to);

                if (!method_exists($orderInvoiceItem, $getterFrom)) {
                    $fieldsNotMigrated[$from] = $to;
                    continue;
                }

                if (!method_exists($orderInvoiceItem, $setterTo)) {
                    $fieldsNotMigrated[$from] = $to;
                    continue;
                }

                $value = $orderInvoiceItem->{$getterFrom}();

                $orderInvoiceItem->{$setterTo}($value);
            }

            $orderInvoiceItem->save();
        }

        foreach ($fieldsNotMigrated as $from => $to) {
            $this->writeMessage(sprintf('Could not migrate %s to %s', $from, $to));
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
