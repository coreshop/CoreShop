<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20210719072811 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $class = $this->container->getParameter('coreshop.model.order_item_unit.class');

        if (class_exists($class)) {
            return;
        }

        $classUpdater = new ClassUpdate($this->container->getParameter());

        $this->write('Create OrderItemUnit Class');
        $jsonFile = $this->container->get('kernel')->locateResource('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderItemUnit.json');
        $this->container->get('coreshop.class_installer')->createClass($jsonFile, 'CoreShopOrderItemUnit');
    }

    public function down(Schema $schema): void
    {

    }
}
