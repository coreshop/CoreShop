<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Console\Output\NullOutput;
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
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up(Schema $schema)
    {
        $this->connection->executeQuery('CREATE TABLE coreshop_address_identifier (`id` INT AUTO_INCREMENT NOT NULL, `active` TINYINT(1) NOT NULL, `name` VARCHAR(255) NOT NULL, `creationDate` DATE NOT NULL, `modificationDate` DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;');
        $this->connection->executeQuery('INSERT INTO `coreshop_address_identifier` (`id`, `active`, `name`, `creationDate`, `modificationDate`) VALUES (1, 1, "shipping", "2019-03-30", "2019-03-30 00:00:00");');
        $this->connection->executeQuery('INSERT INTO `coreshop_address_identifier` (`id`, `active`, `name`, `creationDate`, `modificationDate`) VALUES (2, 1, "invoice", "2019-03-30", "2019-03-30 00:00:00");');

        $addressClass = $this->container->getParameter('coreshop.model.address.pimcore_class_name');
        $classUpdater = new ClassUpdate($addressClass);

        if (!$classUpdater->hasField('addressIdentifier')) {
            $classUpdater->insertFieldAfter('phoneNumber', [
                'fieldtype' => 'coreShopAddressIdentifier',
                'allowEmpty' => true,
                'options' => null,
                'width' => null,
                'defaultValue' => null,
                'optionsProviderClass' => null,
                'optionsProviderData' => null,
                'queryColumnType' => 'varchar',
                'columnType' => 'varchar',
                'columnLength' => 190,
                'phpdocType' => 'string',
                'name' => 'addressIdentifier',
                'title' => 'Address Identifier',
                'tooltip' => '',
                'mandatory' => false,
                'noteditable' => false,
                'index' => false,
                'locked' => null,
                'style' => '',
                'permissions' => null,
                'datatype' => 'data',
                'relationType' => false,
                'invisible' => false,
                'visibleGridView' => false,
                'visibleSearch' => false,
            ]);

            $classUpdater->save();
        }

        //update translations
        $this->container->get('coreshop.resource.installer.shared_translations')->installResources(new NullOutput(), 'coreshop');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
