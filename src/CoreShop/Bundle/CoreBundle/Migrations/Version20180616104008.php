<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180616104008 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE coreshop_cart_price_rule_action DROP FOREIGN KEY FK_830435D887793B6;');
        $this->addSql('DROP INDEX IDX_830435D887793B6 ON coreshop_cart_price_rule_action;');
        $this->addSql('ALTER TABLE coreshop_cart_price_rule_action DROP PRIMARY KEY;');
        $this->addSql('ALTER TABLE coreshop_cart_price_rule_action CHANGE condition_id action_id INT NOT NULL;');
        $this->addSql('ALTER TABLE coreshop_cart_price_rule_action ADD CONSTRAINT FK_830435D9D32F035 FOREIGN KEY (action_id) REFERENCES coreshop_rule_action (id) ON DELETE CASCADE;');
        $this->addSql('CREATE INDEX IDX_830435D9D32F035 ON coreshop_cart_price_rule_action (action_id);');
        $this->addSql('ALTER TABLE coreshop_cart_price_rule_action ADD PRIMARY KEY (price_rule_id, action_id);');
        $this->addSql('ALTER TABLE coreshop_product_price_rule_action DROP FOREIGN KEY FK_47E36FB8887793B6;');
        $this->addSql('DROP INDEX IDX_47E36FB8887793B6 ON coreshop_product_price_rule_action;');
        $this->addSql('ALTER TABLE coreshop_product_price_rule_action DROP PRIMARY KEY;');
        $this->addSql('ALTER TABLE coreshop_product_price_rule_action CHANGE condition_id action_id INT NOT NULL;');
        $this->addSql('ALTER TABLE coreshop_product_price_rule_action ADD CONSTRAINT FK_47E36FB89D32F035 FOREIGN KEY (action_id) REFERENCES coreshop_rule_action (id) ON DELETE CASCADE;');
        $this->addSql('CREATE INDEX IDX_47E36FB89D32F035 ON coreshop_product_price_rule_action (action_id);');
        $this->addSql('ALTER TABLE coreshop_product_price_rule_action ADD PRIMARY KEY (price_rule_id, action_id);');
        $this->addSql('ALTER TABLE coreshop_product_specific_price_rule_action DROP FOREIGN KEY FK_B89BEDCE887793B6;');
        $this->addSql('DROP INDEX IDX_B89BEDCE887793B6 ON coreshop_product_specific_price_rule_action;');
        $this->addSql('ALTER TABLE coreshop_product_specific_price_rule_action DROP PRIMARY KEY;');
        $this->addSql('ALTER TABLE coreshop_product_specific_price_rule_action CHANGE condition_id action_id INT NOT NULL;');
        $this->addSql('ALTER TABLE coreshop_product_specific_price_rule_action ADD CONSTRAINT FK_B89BEDCE9D32F035 FOREIGN KEY (action_id) REFERENCES coreshop_rule_action (id) ON DELETE CASCADE;');
        $this->addSql('CREATE INDEX IDX_B89BEDCE9D32F035 ON coreshop_product_specific_price_rule_action (action_id);');
        $this->addSql('ALTER TABLE coreshop_product_specific_price_rule_action ADD PRIMARY KEY (price_rule_id, action_id);');
        $this->addSql('ALTER TABLE coreshop_exchange_rate CHANGE exchangeRate exchangeRate NUMERIC(10, 5) NOT NULL;');
        $this->addSql('ALTER TABLE coreshop_notification_rule_action DROP FOREIGN KEY FK_D2282E7B887793B6;');
        $this->addSql('DROP INDEX IDX_D2282E7B887793B6 ON coreshop_notification_rule_action;');
        $this->addSql('ALTER TABLE coreshop_notification_rule_action DROP PRIMARY KEY;');
        $this->addSql('ALTER TABLE coreshop_notification_rule_action CHANGE condition_id action_id INT NOT NULL;');
        $this->addSql('ALTER TABLE coreshop_notification_rule_action ADD CONSTRAINT FK_D2282E7B9D32F035 FOREIGN KEY (action_id) REFERENCES coreshop_rule_action (id) ON DELETE CASCADE;');
        $this->addSql('CREATE INDEX IDX_D2282E7B9D32F035 ON coreshop_notification_rule_action (action_id);');
        $this->addSql('ALTER TABLE coreshop_notification_rule_action ADD PRIMARY KEY (notification_id, action_id);');

        $this->container->get('pimcore.cache.core.handler')->clearTag('doctrine_pimcore_cache');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
