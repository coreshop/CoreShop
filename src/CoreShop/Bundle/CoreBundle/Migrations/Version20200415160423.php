<?php
declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Pimcore\BatchProcessing\DataObjectBatchListing;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200415160423 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $this->write('Start migration for Order Invoice Objects');

        $orderInvoiceList = $this->container->get('coreshop.repository.order_invoice')->getList();
        $batchList = new DataObjectBatchListing($orderInvoiceList, 50);

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

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
