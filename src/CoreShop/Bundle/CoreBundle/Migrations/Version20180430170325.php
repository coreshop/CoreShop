<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180430170325 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE coreshop_carrier_translation ADD label VARCHAR(255) DEFAULT NULL;');

        foreach ($this->container->get('coreshop.translation_locale_provider.pimcore')->getDefinedLocalesCodes() as $locale) {
            $this->addSql(sprintf('INSERT INTO coreshop_carrier_translation (translatable_id, locale, label) SELECT id, \'%s\', label FROM coreshop_carrier carrier ON DUPLICATE KEY UPDATE label = carrier.label', $locale));
        }

        $this->addSql('ALTER TABLE coreshop_carrier DROP label;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
