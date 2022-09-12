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

use CoreShop\Component\Order\Model\OrderInvoiceItemInterface;
use CoreShop\Component\Pimcore\BatchProcessing\DataObjectBatchListing;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200415160746 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $orderInvoiceItemList = $this->container->get('coreshop.repository.order_invoice_item')->getList();
        $batchList = new DataObjectBatchListing($orderInvoiceItemList, 50);

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
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
