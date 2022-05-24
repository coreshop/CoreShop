<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Model\Staticroute;

final class Version20220509091354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $existingRoute = Staticroute::getByName('coreshop_checkout_thank_you');

        if (null !== $existingRoute) {
            return;
        }

        $route = new Staticroute();
        $route->setId('coreshop_checkout_thank_you');
        $route->setName('coreshop_checkout_thank_you');
        $route->setPattern('/(\w+)\/shop\/checkout-thank-you\/(.*)$/');
        $route->setReverse('/%_locale/shop/checkout-thank-you/%token');
        $route->setController('CoreShop\Bundle\FrontendBundle\Controller\CheckoutController:thankYouAction');
        $route->setVariables('_locale,token');
        $route->setPriority(2);
        $route->save();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
