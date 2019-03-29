<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20190329100025 extends AbstractPimcoreMigration implements ContainerAwareInterface
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
        $addressClass = $this->container->getParameter('coreshop.model.address.pimcore_class_name');
        $classUpdater = new ClassUpdate($addressClass);

        if (!$classUpdater->hasField('addressType')) {
            $classUpdater->insertFieldAfter('phoneNumber', [
                'fieldtype'       => 'input',
                'width'           => null,
                'queryColumnType' => 'varchar',
                'columnType'      => 'varchar',
                'columnLength'    => 190,
                'phpdocType'      => 'string',
                'regex'           => '',
                'unique'          => false,
                'showCharCount'   => false,
                'name'            => 'addressType',
                'title'           => 'Address Type',
                'tooltip'         => '',
                'mandatory'       => false,
                'noteditable'     => true,
                'index'           => false,
                'locked'          => false,
                'style'           => '',
                'permissions'     => null,
                'datatype'        => 'data',
                'relationType'    => false,
                'invisible'       => false,
                'visibleGridView' => false,
                'visibleSearch'   => false,
            ]);

            $classUpdater->save();
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