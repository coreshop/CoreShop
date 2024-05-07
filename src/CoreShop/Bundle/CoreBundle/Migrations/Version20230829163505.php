<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230829163505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE coreshop_payment_provider_rule_translation (id INT(11) AUTO_INCREMENT NOT NULL, translatable_id INT(11) NOT NULL, label VARCHAR(256) NULL, creationDate DATETIME NOT NULL, modificationDate DATETIME DEFAULT NULL, locale VARCHAR(5), UNIQUE translatable_id_locale (translatable_id, locale), INDEX translatable_id (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB;
            ALTER TABLE coreshop_payment_provider_rule_translation ADD CONSTRAINT FK_9A9D1D4B2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES coreshop_payment_provider_rule (id) ON DELETE CASCADE;
        ');
    }

    public function down(Schema $schema): void
    {
    }
}
