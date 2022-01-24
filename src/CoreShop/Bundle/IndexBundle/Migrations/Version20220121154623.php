<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
