<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180326090909 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $roles = [
            'fieldtype' => 'multiselect',
            'options' => [],
            'width' => '',
            'height' => '',
            'maxItems' => '',
            'optionsProviderClass' => 'CoreShop\\Bundle\\CustomerBundle\\CoreExtension\\Provider\\RoleOptionsProvider',
            'optionsProviderData' => '',
            'queryColumnType' => 'text',
            'columnType' => 'text',
            'phpdocType' => 'array',
            'name' => 'roles',
            'title' => 'roles',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => false,
            'index' => false,
            'locked' => false,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'relationType' => false,
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $customerGroup = $this->container->getParameter('coreshop.model.customer_group.pimcore_class_name');
        $classUpdater = new ClassUpdate($customerGroup);

        if (!$classUpdater->hasField('roles')) {
            $classUpdater->insertFieldAfter('name', $roles);
            $classUpdater->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
