<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\IndexBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Model\Tool\SettingsStore;

class Version20220121154623 extends AbstractMigration
{
    private const INSTALLATION_ID = 'BUNDLE_INSTALLED__CoreShop\Bundle\IndexBundle\CoreShopIndexBundle';

    public function up(Schema $schema): void
    {
        SettingsStore::set(self::INSTALLATION_ID, true, 'bool', 'pimcore');
    }

    public function down(Schema $schema): void
    {
        SettingsStore::set(self::INSTALLATION_ID, false, 'bool', 'pimcore');
    }
}
