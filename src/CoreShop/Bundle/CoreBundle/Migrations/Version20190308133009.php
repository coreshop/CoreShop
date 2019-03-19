<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190308133009 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // migrate store price to store values
        $storePriceRepository = $this->container->get('coreshop.repository.product_store_price');
        $storeValuesRepository = $this->container->get('coreshop.repository.product_store_values');
        $storeValuesFactory = $this->container->get('coreshop.factory.product_store_values');
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        foreach ($storePriceRepository->getAll() as $storePrice) {

            if ($storePrice->getProperty() !== 'storePrice') {
                continue;
            }

            $storePriceProduct = $storePrice->getProduct();
            if (!$storePriceProduct instanceof ProductInterface) {
                continue;
            }

            $storePriceStore = $storePrice->getStore();
            if (!$storePriceStore instanceof StoreInterface) {
                continue;
            }

            $storeValue = $storeValuesRepository->findForProductAndStore($storePriceProduct, $storePriceStore);

            if (is_null($storeValue)) {
                $storeValueEntity = $storeValuesFactory->createNew();
                $storeValueEntity->setStore($storePriceStore);
                $storeValueEntity->setProduct($storePriceProduct);
                $storeValueEntity->setPrice($storePrice->getPrice());
                $entityManager->persist($storeValueEntity);
            } else {
                $storeValue->setPrice($storePrice->getPrice());
                $entityManager->persist($storeValue);
            }
        }

        $entityManager->flush();

        // remove storePrice tag from product class
        $productClass = $this->container->getParameter('coreshop.model.product.pimcore_class_name');
        $classUpdater = new ClassUpdate($productClass);

        if ($classUpdater->hasField('storePrice')) {
            $this->writeMessage(sprintf('You need to drop the StorePrice field manually from the class %s', $productClass));
        }

        $this->writeMessage('You need to drop the coreshop_product_store_price table manually, if you\'re not using the storePrice tag in other object definitions.');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
