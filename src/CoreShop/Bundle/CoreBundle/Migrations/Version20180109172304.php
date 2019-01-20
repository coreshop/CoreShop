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

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\Workflow;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180109172304 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $statusFields = [
            'fieldtype' => 'panel',
            'labelWidth' => 100,
            'layout' => null,
            'name' => 'Status',
            'type' => null,
            'region' => null,
            'title' => 'Status',
            'width' => null,
            'height' => null,
            'collapsible' => false,
            'collapsed' => false,
            'bodyStyle' => '',
            'datatype' => 'layout',
            'permissions' => null,
            'childs' => [
                    [
                        'fieldtype' => 'input',
                        'width' => null,
                        'queryColumnType' => 'varchar',
                        'columnType' => 'varchar',
                        'columnLength' => 190,
                        'phpdocType' => 'string',
                        'regex' => '',
                        'unique' => false,
                        'name' => 'orderState',
                        'title' => 'Order State',
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
                    ],
                    [
                        'fieldtype' => 'input',
                        'width' => null,
                        'queryColumnType' => 'varchar',
                        'columnType' => 'varchar',
                        'columnLength' => 190,
                        'phpdocType' => 'string',
                        'regex' => '',
                        'unique' => false,
                        'name' => 'paymentState',
                        'title' => 'Payment State',
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
                    ],
                    [
                        'fieldtype' => 'input',
                        'width' => null,
                        'queryColumnType' => 'varchar',
                        'columnType' => 'varchar',
                        'columnLength' => 190,
                        'phpdocType' => 'string',
                        'regex' => '',
                        'unique' => false,
                        'name' => 'shippingState',
                        'title' => 'Shipping State',
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
                    ],
                    [
                        'fieldtype' => 'input',
                        'width' => null,
                        'queryColumnType' => 'varchar',
                        'columnType' => 'varchar',
                        'columnLength' => 190,
                        'phpdocType' => 'string',
                        'regex' => '',
                        'unique' => false,
                        'name' => 'invoiceState',
                        'title' => 'Invoice State',
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
                    ],
                ],
            'locked' => null,
        ];

        $product = $this->container->getParameter('coreshop.model.order.pimcore_class_name');
        $classUpdater = new ClassUpdate($product);
        if (!$classUpdater->hasField('orderState')) {
            $classUpdater->insertFieldAfter('Order', $statusFields);
            $classUpdater->save();
        }

        //update translations
        $this->container->get('coreshop.resource.installer.shared_translations')->installResources(new NullOutput(), 'coreshop');

        //delete coreshop workflow
        $list = new Workflow\Listing();
        $list->load();
        $storedWorkflowId = null;
        foreach ($list->getWorkflows() as $workflow) {
            if ($workflow->getName() === 'OrderState') {
                $storedWorkflowId = $workflow->getId();
                $workflow->delete();
            }
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
