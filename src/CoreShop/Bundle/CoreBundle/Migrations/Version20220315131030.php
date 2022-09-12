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

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220315131030 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        if ($schema->getTable('coreshop_carrier')->hasColumn('isFree')) {
            $this->addSql('ALTER TABLE coreshop_carrier DROP COLUMN `isFree`');
        }
    }

    public function down(Schema $schema): void
    {
        // do nothing due to potential data loss
    }
}
