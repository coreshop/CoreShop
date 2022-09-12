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

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use CoreShop\Component\Pimcore\DataObject\FieldCollectionDefinitionUpdate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200414153349 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $classes = [
            $this->container->getParameter('coreshop.model.address.pimcore_class_name') => [],
            $this->container->getParameter('coreshop.model.category.pimcore_class_name') => [
                'parentCategory' => ['fieldtype' => 'coreShopRelation', 'stack' => 'coreshop.category'],
            ],
            $this->container->getParameter('coreshop.model.company.pimcore_class_name') => [
                'addresses' => ['fieldtype' => 'coreShopRelations', 'stack' => 'coreshop.address'],
            ],
            $this->container->getParameter('coreshop.model.customer.pimcore_class_name') => [
                'company' => ['fieldtype' => 'coreShopRelation', 'stack' => 'coreshop.company'],
                'defaultAddress' => ['fieldtype' => 'coreShopRelation', 'stack' => 'coreshop.address'],
                'addresses' => ['fieldtype' => 'coreShopRelations', 'stack' => 'coreshop.address'],
                'customerGroups' => ['fieldtype' => 'coreShopRelations', 'stack' => 'coreshop.customer_group'],
            ],
            $this->container->getParameter('coreshop.model.customer_group.pimcore_class_name') => [],
            $this->container->getParameter('coreshop.model.manufacturer.pimcore_class_name') => [],
            $this->container->getParameter('coreshop.model.order_invoice.pimcore_class_name') => [
                'order' => ['fieldtype' => 'coreShopRelation', 'stack' => 'coreshop.order'],
                'items' => ['fieldtype' => 'coreShopRelations', 'stack' => 'coreshop.order_invoice_item'],
            ],
            $this->container->getParameter('coreshop.model.order_invoice_item.pimcore_class_name') => [
                'orderItem' => ['fieldtype' => 'coreShopRelation', 'stack' => 'coreshop.order_item'],
            ],
            $this->container->getParameter('coreshop.model.order_shipment.pimcore_class_name') => [
                'order' => ['fieldtype' => 'coreShopRelation', 'stack' => 'coreshop.order'],
                'items' => ['fieldtype' => 'coreShopRelations', 'stack' => 'coreshop.order_shipment_item'],
            ],
            $this->container->getParameter('coreshop.model.order_shipment_item.pimcore_class_name') => [
                'orderItem' => ['fieldtype' => 'coreShopRelation', 'stack' => 'coreshop.order_item'],
            ],
            $this->container->getParameter('coreshop.model.product.pimcore_class_name') => [
                'manufacturer' => ['fieldtype' => 'coreShopRelation', 'stack' => 'coreshop.manufacturer'],
                'categories' => ['fieldtype' => 'coreShopRelations', 'stack' => 'coreshop.category'],
            ],
            $this->container->getParameter('coreshop.model.order.pimcore_class_name') => [
                'customer' => ['fieldtype' => 'coreShopRelation', 'stack' => 'coreshop.customer'],
                'shippingAddress' => ['fieldtype' => 'coreShopRelation', 'stack' => 'coreshop.address'],
                'invoiceAddress' => ['fieldtype' => 'coreShopRelation', 'stack' => 'coreshop.address'],
                'items' => ['fieldtype' => 'coreShopRelations', 'stack' => 'coreshop.order_item'],
            ],
            $this->container->getParameter('coreshop.model.order_item.pimcore_class_name') => [
                'product' => ['fieldtype' => 'coreShopRelation', 'stack' => 'coreshop.purchasable'],
            ],
        ];

        $fcs = [
            $this->container->getParameter('coreshop.model.tax_item.pimcore_class_name'),
            $this->container->getParameter('coreshop.model.adjustment.pimcore_class_name'),
            $this->container->getParameter('coreshop.model.cart_price_rule_item.pimcore_class_name'),
        ];

        foreach ($classes as $class => $fields) {
            $this->write('Migrate class ' . $class);

            $classUpdater = new ClassUpdate($class);

            if (!$classUpdater->getProperty('generateTypeDeclarations')) {
                $classUpdater->setProperty('generateTypeDeclarations', true);
            }

            foreach ($fields as $field => $config) {
                if ($classUpdater->hasField($field)) {
                    $classUpdater->replaceFieldProperties($field, $config);
                }
            }

            $classUpdater->save();

            ClassDefinition::getByName($class)->save();
        }

        foreach ($fcs as $fc) {
            $this->write('Migrate fc ' . $fc);

            $classUpdater = new FieldCollectionDefinitionUpdate($fc);

            $classUpdater->setProperty('generateTypeDeclarations', true);
            $classUpdater->save();

            Fieldcollection\Definition::getByKey($fc)->save();
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
