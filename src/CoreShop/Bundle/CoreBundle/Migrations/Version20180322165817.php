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

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180322165817 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->container->get('coreshop.resource.installer.shared_translations')->installResources(new NullOutput(), 'coreshop');

        //update static routes (customer newsletter confirm route added)
        $options = ['allowed' => ['coreshop_customer_confirm_newsletter']];
        $this->container->get('coreshop.resource.installer.routes')->installResources(new NullOutput(), 'coreshop', $options);

        $fieldDefinition = [
            'fieldtype' => 'input',
            'width' => null,
            'queryColumnType' => 'varchar',
            'columnType' => 'varchar',
            'columnLength' => 190,
            'phpdocType' => 'string',
            'regex' => '',
            'unique' => false,
            'name' => 'newsletterToken',
            'title' => 'Newsletter Token',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => true,
            'index' => false,
            'locked' => null,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'relationType' => false,
            'invisible' => true,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $customerClass = $this->container->getParameter('coreshop.model.customer.pimcore_class_name');
        $classUpdate = new ClassUpdate($customerClass);

        if (!$classUpdate->hasField('newsletterToken')) {
            $classUpdate->insertFieldAfter('newsletterConfirmed', $fieldDefinition);
            $classUpdate->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
