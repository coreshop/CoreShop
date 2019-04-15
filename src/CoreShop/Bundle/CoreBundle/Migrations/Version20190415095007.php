<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Cache;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;

class Version20190415095007 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $collectionFieldName = 'priceRuleItems';

        /** @var PimcoreRepositoryInterface $cartRepo */
        $cartRepo = $this->container->get('coreshop.repository.cart');
        /** @var PimcoreRepositoryInterface $orderRepo */
        $orderRepo = $this->container->get('coreshop.repository.order');
        /** @var PimcoreRepositoryInterface $quoteClassname */
        $quoteRepo = $this->container->get('coreshop.repository.quote');

        $tables = [
            sprintf('object_collection_CoreShopProposalCartPriceRuleItem_%d', $cartRepo->getClassId()),
            sprintf('object_collection_CoreShopProposalCartPriceRuleItem_%d', $orderRepo->getClassId()),
            sprintf('object_collection_CoreShopProposalCartPriceRuleItem_%d', $quoteRepo->getClassId()),
        ];

        foreach ($tables as $tableName) {

            if (!$schema->hasTable($tableName)) {
                continue;
            }

            $items = $this->connection->fetchAll(sprintf('SELECT * FROM %s WHERE `fieldname` = "%s"', $tableName, $collectionFieldName));
            if (!is_array($items) || count($items) === 0) {
                continue;
            }

            foreach ($items as $item) {

                $executeQuery = false;

                $index = $item['index'];
                $objectId = $item['o_id'];
                $fieldName = $item['fieldname'];
                $discountNet = $item['discountNet'];
                $discountGross = $item['discountGross'];

                if (is_numeric($discountNet)) {
                    $discountNet = (int) $discountNet;
                }

                if (is_numeric($discountGross)) {
                    $discountGross = (int) $discountGross;
                }

                if ($discountNet > 0) {
                    $executeQuery = true;
                    $discountNet = -1 * $discountNet;
                }

                if ($discountGross > 0) {
                    $executeQuery = true;
                    $discountGross = -1 * $discountGross;
                }

                if ($executeQuery === true) {
                    $this->addSql(sprintf(
                            'UPDATE `%s` SET `discountNet` = %d, `discountGross` = %d WHERE `o_id` = "%d" AND `index` = "%d" AND `fieldname` = "%s";',
                            $tableName,
                            $discountNet, $discountGross,
                            $objectId, $index, $fieldName
                        )
                    );
                }
            }
        }

        // update translations
        $this->container->get('coreshop.resource.installer.shared_translations')->installResources(new NullOutput(), 'coreshop');

        // clear cache
        Cache::clearAll();
        Cache\Runtime::clear();

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
