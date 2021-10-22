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

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20191122074930 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        if (!$schema->getTable('coreshop_carrier')->hasColumn('taxCalculationStrategy')) {
            $this->addSql('ALTER TABLE coreshop_carrier ADD `taxCalculationStrategy` VARCHAR(255) DEFAULT NULL AFTER logo;');
            $this->addSql("UPDATE coreshop_carrier SET `taxCalculationStrategy` = 'taxRule' WHERE `taxRuleGroupId` IS NOT NULL;");

            $this->container->get('pimcore.cache.core.handler')->clearTag('doctrine_pimcore_cache');
        }
    }

    public function down(Schema $schema): void
    {
        // do nothing due to potential data loss
    }
}
