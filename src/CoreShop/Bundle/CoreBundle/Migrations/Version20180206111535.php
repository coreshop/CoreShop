<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Index\Model\FilterConditionInterface;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180206111535 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $filterConditions = $this->container->get('coreshop.repository.filter_condition')->findAll();
        $em = $this->container->get('coreshop.manager.filter_condition');

        foreach ($filterConditions as $filterCondition) {
            if (!$filterCondition instanceof FilterConditionInterface) {
                continue;
            }

            $config = $filterCondition->getConfiguration();
            $config['field'] = $filterCondition->getField();

            $filterCondition->setConfiguration($config);

            $em->persist($filterCondition);
        }


        $em->flush();

        $db  = $this->container->get('doctrine.dbal.default_connection')->executeQuery('ALTER TABLE coreshop_filter_condition CHANGE field field VARCHAR(255) DEFAULT NULL;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
