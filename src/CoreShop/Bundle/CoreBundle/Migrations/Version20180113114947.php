<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180113114947 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $stateField = [
            'fieldtype' => 'input',
            'width' => null,
            'queryColumnType' => 'varchar',
            'columnType' => 'varchar',
            'columnLength' => 190,
            'phpdocType' => 'string',
            'regex' => '',
            'unique' => false,
            'name' => 'state',
            'title' => 'State',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => true,
            'index' => false,
            'locked' => null,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'relationType' => false,
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $orderShipment = $this->container->getParameter('coreshop.model.order_shipment.pimcore_class_name');
        $classUpdater = new ClassUpdate($orderShipment);
        if (!$classUpdater->hasField('state')) {
            $classUpdater->insertFieldAfter('shipmentNumber', $stateField);
            $classUpdater->save();
        }

        $orderInvoice = $this->container->getParameter('coreshop.model.order_invoice.pimcore_class_name');
        $classUpdater = new ClassUpdate($orderInvoice);
        if (!$classUpdater->hasField('state')) {
            $classUpdater->insertFieldAfter('invoiceNumber', $stateField);
            $classUpdater->save();
        }

        //update static routes (order controller added)
        $this->container->get('coreshop.resource.installer.routes')->installResources(new NullOutput(), 'coreshop');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}