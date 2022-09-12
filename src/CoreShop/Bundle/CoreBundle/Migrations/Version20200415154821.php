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

use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Pimcore\BatchProcessing\DataObjectBatchListing;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200415154821 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $orderItemList = $this->container->get('coreshop.repository.order_item')->getList();
        $batchList = new DataObjectBatchListing($orderItemList, 50);

        $fieldMap = [
            'itemWholesalePrice' => 'convertedItemWholesalePrice',
            'itemDiscountNet' => 'convertedItemDiscountNet',
            'itemDiscountGross' => 'convertedItemDiscountGross',
            'itemDiscountPriceNet' => 'convertedItemDiscountPriceNet',
            'itemDiscountPriceGross' => 'convertedItemDiscountPriceGross',
            'itemPriceNet' => 'convertedItemPriceNet',
            'itemPriceGross' => 'convertedItemPriceGross',
            'itemRetailPriceNet' => 'convertedItemRetailPriceNet',
            'itemRetailPriceGross' => 'convertedItemRetailPriceGross',
            'totalNet' => 'convertedTotalNet',
            'totalGross' => 'convertedTotalGross',
            'itemTax' => 'convertedItemTax',
            'taxes' => 'convertedTaxes',
            'pimcoreAdjustmentTotalNet' => 'convertedPimcoreAdjustmentTotalNet',
            'pimcoreAdjustmentTotalGross' => 'convertedPimcoreAdjustmentTotalGross',
            'adjustmentItems' => 'convertedAdjustmentItems',

            'baseItemWholesalePrice' => 'itemWholesalePrice',
            'baseItemDiscountNet' => 'itemDiscountNet',
            'baseItemDiscountGross' => 'itemDiscountGross',
            'baseItemDiscountPriceNet' => 'itemDiscountPriceNet',
            'baseItemDiscountPriceGross' => 'itemDiscountPriceGross',
            'baseItemPriceNet' => 'itemPriceNet',
            'baseItemPriceGross' => 'itemPriceGross',
            'baseItemRetailPriceNet' => 'itemRetailPriceNet',
            'baseItemRetailPriceGross' => 'itemRetailPriceGross',
            'baseTotalNet' => 'totalNet',
            'baseTotalGross' => 'totalGross',
            'baseItemTax' => 'itemTax',
            'baseTaxes' => 'taxes',
            'basePimcoreAdjustmentTotalNet' => 'pimcoreAdjustmentTotalNet',
            'basePimcoreAdjustmentTotalGross' => 'pimcoreAdjustmentTotalGross',
            'baseAdjustmentItems' => 'adjustmentItems',
        ];

        $fieldsNotMigrated = [];

        foreach ($batchList as $orderItem) {
            if (!$orderItem instanceof OrderItemInterface) {
                continue;
            }

            foreach ($fieldMap as $from => $to) {
                $getterFrom = 'get' . ucfirst($from);
                $setterTo = 'set' . ucfirst($to);

                if (!method_exists($orderItem, $getterFrom)) {
                    $fieldsNotMigrated[$from] = $to;

                    continue;
                }

                if (!method_exists($orderItem, $setterTo)) {
                    $fieldsNotMigrated[$from] = $to;

                    continue;
                }

                $value = $orderItem->{$getterFrom}();

                $orderItem->{$setterTo}($value);
            }

            $orderItem->save();
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
