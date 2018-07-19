<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Bundle\CoreBundle\CoreExtension\StorePrice;
use CoreShop\Bundle\ProductBundle\CoreExtension\ProductSpecificPriceRules;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180719151524 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $classes = new ClassDefinition\Listing();
        $classes = $classes->load();

        foreach ($classes as $class) {
            foreach ($class->getFieldDefinitions() as $definition) {
                if ($definition instanceof StorePrice || $definition instanceof ProductSpecificPriceRules) {
                    $class->save();
                    break;
                }
            }
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
