<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Pimcore\BatchProcessing\BatchListing;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200415153638 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->writeMessage('Start migration for Order Objects');

        $orderList = $this->container->get('coreshop.repository.order')->getList();
        $batchList = new BatchListing($orderList, 50);

        $fieldMap = [
            'paymentTotal' => 'convertedPaymentTotal',
            'totalNet' => 'convertedTotalNet',
            'totalGross' => 'convertedTotalGross',
            'subtotalNet' => 'convertedSubtotalNet',
            'subtotalGross' => 'convertedSubtotalGross',
            'shippingTaxRate' => 'convertedShippingTaxRate',
            'taxes' => 'convertedTaxes',
            'pimcoreAdjustmentTotalNet' => 'convertedPimcoreAdjustmentTotalNet',
            'pimcoreAdjustmentTotalGross' => 'convertedPimcoreAdjustmentTotalGross',
            'adjustmentItems' => 'convertedAdjustmentItems',

            'baseTotalNet' => 'totalNet',
            'baseTotalGross' => 'totalGross',
            'baseSubtotalNet' => 'subtotalNet',
            'baseSubtotalGross' => 'subtotalGross',
            'baseShippingTaxRate' => 'shippingTaxRate',
            'baseTaxes' => 'taxes',
            'basePimcoreAdjustmentTotalNet' => 'pimcoreAdjustmentTotalNet',
            'basePimcoreAdjustmentTotalGross' => 'pimcoreAdjustmentTotalGross',
            'baseAdjustmentItems' => 'adjustmentItems',
        ];

        $fieldsNotMigrated = [];

        foreach ($batchList as $order) {
            if (!$order instanceof OrderInterface) {
                continue;
            }

            $order->setSaleState(OrderSaleStates::STATE_ORDER);

            foreach ($fieldMap as $from => $to) {
                $getterFrom = 'get' . ucfirst($from);
                $setterTo = 'set' . ucfirst($to);

                if (!method_exists($order, $getterFrom)) {
                    $fieldsNotMigrated[$from] = $to;
                    continue;
                }

                if (!method_exists($order, $setterTo)) {
                    $fieldsNotMigrated[$from] = $to;
                    continue;
                }

                $value = $order->{$getterFrom}();

                $order->{$setterTo}($value);
            }

            $order->save();
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
