<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180323160716 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     * @throws \CoreShop\Component\Pimcore\ClassDefinitionNotFoundException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $this->container->get('coreshop.resource.installer.shared_translations')->installResources(new NullOutput(), 'coreshop');

        $fieldDefinition = [
            'fieldtype'       => 'input',
            'width'           => null,
            'queryColumnType' => 'varchar',
            'columnType'      => 'varchar',
            'columnLength'    => 190,
            'phpdocType'      => 'string',
            'regex'           => '',
            'unique'          => false,
            'name'            => 'salutation',
            'title'           => 'Salutation',
            'tooltip'         => '',
            'mandatory'       => false,
            'noteditable'     => false,
            'index'           => false,
            'locked'          => null,
            'style'           => '',
            'permissions'     => null,
            'datatype'        => 'data',
            'relationType'    => false,
            'invisible'       => false,
            'visibleGridView' => false,
            'visibleSearch'   => false,
        ];

        $addressClass = $this->container->getParameter('coreshop.model.address.pimcore_class_name');
        $classUpdate = new ClassUpdate($addressClass);

        if (!$classUpdate->hasField('salutation')) {
            $classUpdate->insertFieldBefore('firstname', $fieldDefinition);
            $classUpdate->save();
        }

        $customerClass = $this->container->getParameter('coreshop.model.customer.pimcore_class_name');
        $classUpdate = new ClassUpdate($customerClass);

        if (!$classUpdate->hasField('salutation')) {
            $classUpdate->insertFieldBefore('firstname', $fieldDefinition);
            $classUpdate->save();
        }

        //add country salutation prefix
        if ($schema->hasTable('coreshop_country')) {
            $table = $schema->getTable('coreshop_country');
            if (!$table->hasColumn('salutationPrefix')) {
                $table->addColumn('salutationPrefix', 'string', ['expose' => true, 'groups' => ['Detailed']]);
            }
        }

        $this->container->get('pimcore.cache.core.handler')->clearTag('doctrine_pimcore_cache');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}