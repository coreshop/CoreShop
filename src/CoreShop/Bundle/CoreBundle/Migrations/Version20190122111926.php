<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

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
                array_walk_recursive($actualConfig, function (&$value, $key) {
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
