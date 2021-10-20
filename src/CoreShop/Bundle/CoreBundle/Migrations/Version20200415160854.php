<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Core\Model\OrderShipmentItemInterface;
use CoreShop\Component\Pimcore\BatchProcessing\BatchListing;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200415160854 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $orderShipmentItemList = $this->container->get('coreshop.repository.order_shipment_item')->getList();
        $batchList = new BatchListing($orderShipmentItemList, 50);

        $fieldMap = [
            'totalNet' => 'convertedTotalNet',
            'totalGross' => 'convertedTotalGross',

            'baseTotalNet' => 'totalNet',
            'baseTotalGross' => 'totalGross',
        ];

        $fieldsNotMigrated = [];

        foreach ($batchList as $orderShipmentItem) {
            if (!$orderShipmentItem instanceof OrderShipmentItemInterface) {
                continue;
            }

            foreach ($fieldMap as $from => $to) {
                $getterFrom = 'get' . ucfirst($from);
                $setterTo = 'set' . ucfirst($to);

                if (!method_exists($orderShipmentItem, $getterFrom)) {
                    $fieldsNotMigrated[$from] = $to;
                    continue;
                }

                if (!method_exists($orderShipmentItem, $setterTo)) {
                    $fieldsNotMigrated[$from] = $to;
                    continue;
                }

                $value = $orderShipmentItem->{$getterFrom}();

                $orderShipmentItem->{$setterTo}($value);
            }

            $orderShipmentItem->save();
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
