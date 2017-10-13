<?php

namespace CoreShop\Bundle\AdminBundle\Migrations;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Pimcore\ClassUpdate;
use CoreShop\Component\Store\Model\StoreInterface;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20171013073325 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $className = "CoreShopProduct";

        $newField = [
            "fieldtype" => "coreShopStorePrice",
            "width" => "",
            "defaultValue" => null,
            "queryColumnType" => "text",
            "columnType" => "text",
            "phpdocType" => "array",
            "minValue" => null,
            "maxValue" => null,
            "name" => "storePrice",
            "title" => "Store Price",
            "tooltip" => "",
            "mandatory" => false,
            "noteditable" => false,
            "index" => false,
            "locked" => false,
            "style" => "",
            "permissions" => null,
            "datatype" => "data",
            "relationType" => false,
            "invisible" => false,
            "visibleGridView" => false,
            "visibleSearch" => false
        ];

        $classUpdater = new ClassUpdate($className);

        if (!$classUpdater->hasField('storePrice')) {
            $classUpdater->insertFieldBefore('wholesalePrice', $newField);

            if ($classUpdater->hasField('pimcoreBasePrice')) {
                $classUpdater->replaceFieldProperties('pimcoreBasePrice', ['noteditable' => true]);
            }

            $classUpdater->save();
        }

        $products = $this->container->get('coreshop.repository.product')->findAll();
        $stores = $this->container->get('coreshop.repository.store')->findAll();

        /**
         * @var $product ProductInterface
         * @var $store StoreInterface
         */
        foreach ($products as $product) {
            foreach ($stores as $store) {
                if (method_exists($product, 'getPimcoreBasePrice')) {
                    $product->setStorePrice($product->getPimcoreBasePrice(), $store);
                }
            }

            $product->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // We don't do any down migration, we don't want to destroy any existing data here!
    }
}
