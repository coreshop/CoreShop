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

class Version20200211120529 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     *
     * @throws \CoreShop\Component\Pimcore\Exception\ClassDefinitionFieldNotFoundException
     * @throws \CoreShop\Component\Pimcore\Exception\ClassDefinitionNotFoundException
     */
    public function up(Schema $schema)
    {
        $this->writeMessage('Create Company Class');
        $jsonFile = $this->container->get('kernel')->locateResource('@CoreShopCoreBundle/Resources/install/pimcore/classes/CoreShopCustomerBundle/CoreShopCompany.json');
        $this->container->get('coreshop.class_installer')->createClass($jsonFile, 'CoreShopCompany');

        $companyField = [
            'fieldtype' => 'manyToOneRelation',
            'width' => '',
            'assetUploadPath' => '',
            'relationType' => true,
            'queryColumnType' => [
                'id' => 'int(11)',
                'type' => 'enum("document","asset","object")',
            ],
            'phpdocType' => '\\Pimcore\\Model\\Document\\Page | \\Pimcore\\Model\\Document\\Snippet | \\Pimcore\\Model\\Document | \\Pimcore\\Model\\Asset | \\Pimcore\\Model\\DataObject\\AbstractObject',
            'objectsAllowed' => true,
            'assetsAllowed' => false,
            'assetTypes' => [],
            'documentsAllowed' => false,
            'documentTypes' => [],
            'lazyLoading' => true,
            'classes' => [
                'CoreShopCompany',
            ],
            'pathFormatterClass' => '',
            'name' => 'company',
            'title' => 'Company',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => false,
            'index' => false,
            'locked' => false,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $addressAccessTypeField = [
             'fieldtype' => 'select',
             'options' => [],
             'width' => '',
             'defaultValue' => '',
             'optionsProviderClass' => '@CoreShop\\Component\\Core\\Customer\\Address\\AddressAccessOptionsProvider',
             'optionsProviderData' => '',
             'queryColumnType' => 'varchar',
             'columnType' => 'varchar',
             'columnLength' => 190,
             'phpdocType' => 'string',
             'dynamicOptions' => false,
             'name' => 'addressAccessType',
             'title' => 'Address Access Type',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => false,
             'index' => false,
             'locked' => NULL,
             'style' => '',
             'permissions' => NULL,
             'datatype' => 'data',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
        ];

        $saveClass = false;
        $classUpdater = new ClassUpdate($this->container->getParameter('coreshop.model.customer.pimcore_class_name'));

        if (!$classUpdater->hasField('company')) {
            $classUpdater->insertFieldAfter('lastname', $companyField);
            $saveClass = true;
        }

        if (!$classUpdater->hasField('addressAccessType')) {
            $classUpdater->insertFieldAfter('addresses', $addressAccessTypeField);
            $saveClass = true;
        }

        if ($saveClass === true) {
            $this->writeMessage('Update Customer Class');
            $classUpdater->save();
        }

        $this->writeMessage('Update Admin Translations');
        $this->container->get('coreshop.resource.installer.admin_translations')->installResources(new NullOutput(), 'coreshop');

        $this->writeMessage('Update Website Translations');
        $this->container->get('coreshop.resource.installer.shared_translations')->installResources(new NullOutput(), 'coreshop');

        $this->writeMessage('Update Permissions');
        $table = $schema->getTable('users_permission_definitions');

        if ($table->hasColumn('category')) {
            $this->addSql('INSERT INTO `users_permission_definitions` (`key`, `category`) VALUES (\'coreshop_permission_ctc_assign_to_new\', \'\');');
            $this->addSql('INSERT INTO `users_permission_definitions` (`key`, `category`) VALUES (\'coreshop_permission_ctc_assign_to_existing\', \'\');');
        } else {
            $this->addSql('INSERT INTO `users_permission_definitions` (`key`) VALUES (\'coreshop_permission_ctc_assign_to_new\');');
            $this->addSql('INSERT INTO `users_permission_definitions` (`key`) VALUES (\'coreshop_permission_ctc_assign_to_existing\');');
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
