<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\Migration\SharedTranslation;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180731140144 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        SharedTranslation::add('coreshop.ui.checkout.thank_you', 'de_CH', 'Vielen Dank für Ihre Bestellung');
        SharedTranslation::add('coreshop.ui.checkout.thank_you', 'de', 'Vielen Dank für Ihre Bestellung');
        SharedTranslation::add('coreshop.ui.checkout.thank_you', 'en', 'Thank you for your Order');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
