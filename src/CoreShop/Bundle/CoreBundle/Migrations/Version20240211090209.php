<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Bundle\StaticRoutesBundle\Model\Staticroute;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211090209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $route = Staticroute::getByName('coreshop_payment_token');

        if ($route) {
            $route->setPriority(3);
            $route->save();

            return;
        }

        $this->write('Static route "coreshop_payment_token" not found, make sure the priority is set to 3');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
