<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Order\Model\OrderShipmentItem;
use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20190410180305 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $shipmentItem = $this->container->getParameter('coreshop.model.order_shipment_item.pimcore_class_name');
        $classUpdater = new ClassUpdate($shipmentItem);

        if ($classUpdater->getProperty('parentClass') === OrderShipmentItem::class) {
            $classUpdater->setProperty('parentClass', \CoreShop\Component\Core\Model\OrderShipmentItem::class);
            $classUpdater->save();
        } else {
            $this->write(
                sprintf(
                    '<error>You use a non default parent-class in your %s class, please make sure to inherit from %s now.</error>',
                    $shipmentItem,
                    \CoreShop\Component\Core\Model\OrderShipmentItem::class
                )
            );
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
