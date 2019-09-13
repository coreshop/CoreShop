<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Bundle\ResourceBundle\Installer\Configuration\GridConfigConfiguration;
use CoreShop\Component\Pimcore\DataObject\GridConfigInstaller;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Yaml\Yaml;

class Version20190430153230 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO `users_permission_definitions` (`key`, `category`) VALUES (\'coreshop_permission_cart_list\', \'\');');
        $this->addSql('INSERT INTO `users_permission_definitions` (`key`, `category`) VALUES (\'coreshop_permission_cart_create\', \'\');');

        $cartClassId = $this->container->get('coreshop.repository.cart')->getClassId();

        $file = $this->container->get('kernel')->locateResource('@CoreShopOrderBundle/Resources/install/pimcore/grid-config.yml');

        if (file_exists($file)) {
            $processor = new Processor();
            $configurationDefinition = new GridConfigConfiguration();
            $gridConfigInstaller = new GridConfigInstaller();

            $gridConfigs = Yaml::parse(file_get_contents($file));
            $gridConfigs = $processor->processConfiguration($configurationDefinition, ['grid_config' => $gridConfigs]);
            $gridConfigs = $gridConfigs['grid_config'];

            foreach ($gridConfigs as $name => $gridConfigData) {
                if (!in_array($name, ['cart_de', 'cart_en'], true)) {
                    continue;
                }

                $gridConfigInstaller->installGridConfig($gridConfigData['data'], $gridConfigData['name'], $cartClassId, true);
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
