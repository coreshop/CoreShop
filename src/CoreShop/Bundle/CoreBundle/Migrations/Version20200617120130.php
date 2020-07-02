<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200617120130 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE coreshop_cart_price_rule_voucher_code ADD creditUsed INT NOT NULL, ADD isCreditCode TINYINT(1) NOT NULL;
            ALTER TABLE coreshop_cart_price_rule_voucher_code ADD creditAvailable INT NOT NULL, ADD currencyId INT DEFAULT NULL;
            ALTER TABLE coreshop_cart_price_rule_voucher_code ADD CONSTRAINT FK_4AF500A991000B8A FOREIGN KEY (currencyId) REFERENCES coreshop_currency (id) ON DELETE SET NULL;
            CREATE INDEX IDX_4AF500A991000B8A ON coreshop_cart_price_rule_voucher_code (currencyId);
        ');

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
