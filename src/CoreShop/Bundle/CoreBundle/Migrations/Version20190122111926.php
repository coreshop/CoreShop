<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\GridConfig;

class Version20190122111926 extends AbstractPimcoreMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $list = new GridConfig\Listing();
        $list->load();

        /**
         * @var GridConfig $config
         */
        foreach ($list->getGridConfigs() as $config) {
            $actualConfig = $config->getConfig();
            $actualConfig = json_decode($actualConfig, true);

            if (is_array($actualConfig)) {
                array_walk_recursive($actualConfig, function(&$value, $key) {
                    if ($key !== 'class') {
                        return;
                    }

                    switch ($value) {
                        case 'OrderState':
                            $value = 'coreshop_order_state';
                            break;

                        case 'PriceFormatter':
                            $value = 'coreshop_price_formatter';
                            break;
                    }
                });

                $config->setConfig(json_encode($actualConfig));
                $config->save();
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
