<?php

namespace CoreShop\Bundle\AdminBundle\Migrations;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20171013181622 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $repositories = [
            $this->container->get('coreshop.repository.shipping_rule'),
            $this->container->get('coreshop.repository.product_price_rule'),
            $this->container->get('coreshop.repository.product_specific_price_rule'),
            $this->container->get('coreshop.repository.cart_price_rule')
        ];
        $defaultStore = $this->container->get('coreshop.repository.store')->findStandard();
        $defaultStoreCurrencyId = $defaultStore->getCurrency()->getId();
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $actionTypes = [
            'price',
            'discountAmount',
            'price',
            'additionAmount'
        ];

        /**
         * @var $repo RepositoryInterface
         */
        foreach ($repositories as $repo) {
            $elements = $repo->findAll();

            /**
             * @var $element RuleInterface
             */
            foreach ($elements as $element) {
                $actions = $element->getActions();
                $changesMade = false;

                foreach ($actions as $action) {
                    if (in_array($action->getType(), $actionTypes)) {
                        $configuration = $action->getConfiguration();

                        if (!array_key_exists('currency', $configuration)) {
                            $configuration['currency'] = $defaultStoreCurrencyId;

                            $action->setConfiguration($configuration);
                            $changesMade = true;
                        }
                    }
                }

                if ($changesMade) {
                    $entityManager->persist($element);
                }
            }
        }

        $entityManager->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
