<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Pimcore\BatchProcessing\BatchListing;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200415160423 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->writeMessage('Start migration for Order Invoice Objects');

        $orderInvoiceList = $this->container->get('coreshop.repository.order_invoice')->getList();
        $batchList = new BatchListing($orderInvoiceList, 50);

        $fieldMap = [
            'totalNet' => 'convertedTotalNet',
            'totalGross' => 'convertedTotalGross',
            'subtotalNet' => 'convertedSubtotalNet',
            'subtotalGross' => 'convertedSubtotalGross',
            'shippingTaxRate' => 'convertedShippingTaxRate',
            'pimcoreAdjustmentTotalNet' => 'convertedPimcoreAdjustmentTotalNet',
            'pimcoreAdjustmentTotalGross' => 'convertedPimcoreAdjustmentTotalGross',
            'adjustmentItems' => 'convertedAdjustmentItems',

            'baseTotalNet' => 'totalNet',
            'baseTotalGross' => 'totalGross',
            'baseSubtotalNet' => 'subtotalNet',
            'baseShippingTaxRate' => 'shippingTaxRate',
            'basePimcoreAdjustmentTotalNet' => 'pimcoreAdjustmentTotalNet',
            'basePimcoreAdjustmentTotalGross' => 'pimcoreAdjustmentTotalGross',
            'baseAdjustmentItems' => 'adjustmentItems',
        ];

        $fieldsNotMigrated = [];

        foreach ($batchList as $orderInvoice) {
            if (!$orderInvoice instanceof OrderInvoiceInterface) {
                continue;
            }

            foreach ($fieldMap as $from => $to) {
                $getterFrom = 'get' . ucfirst($from);
                $setterTo = 'set' . ucfirst($to);

                if (!method_exists($orderInvoice, $getterFrom)) {
                    $fieldsNotMigrated[$from] = $to;
                    continue;
                }

                if (!method_exists($orderInvoice, $setterTo)) {
                    $fieldsNotMigrated[$from] = $to;
                    continue;
                }

                $value = $orderInvoice->{$getterFrom}();

                $orderInvoice->{$setterTo}($value);
            }

            $orderInvoice->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
