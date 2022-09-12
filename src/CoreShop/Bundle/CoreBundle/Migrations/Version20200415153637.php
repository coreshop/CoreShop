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

final class Version20200415153637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add `hideFromCheckout` field in `coreshop_carrier`';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->getTable('coreshop_carrier')->hasColumn('hideFromCheckout')) {
            $this->addSql('
                ALTER TABLE coreshop_carrier ADD hideFromCheckout TINYINT(1) NOT NULL AFTER trackingUrl;
            ');
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->getTable('coreshop_carrier')->hasColumn('hideFromCheckout')) {
            $this->addSql('
                ALTER TABLE coreshop_carrier DROP hideFromCheckout;
            ');
        }
    }
}
