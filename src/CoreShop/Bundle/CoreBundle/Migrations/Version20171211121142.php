<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20171211121142 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     * @throws \CoreShop\Component\Pimcore\ClassDefinitionFieldNotFoundException
     */
    public function up(Schema $schema)
    {
        $localeField = [
            'fieldtype' => 'input',
            'width' => null,
            'queryColumnType' => 'varchar',
            'columnType' => 'varchar',
            'columnLength' => 190,
            'phpdocType' => 'string',
            'regex' => '',
            'unique' => false,
            'name' => 'locale',
            'title' => 'Locale',
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
            'visibleSearch' => false
        ];

        $customer = $this->container->getParameter('coreshop.model.customer.pimcore_class_name');
        $classUpdater = new ClassUpdate($customer);
        if (!$classUpdater->hasField('locale')) {
            $classUpdater->insertFieldAfter('passwordResetHash', $localeField);
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
