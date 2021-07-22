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

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180613194714 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE coreshop_payment_provider_translation CHANGE name title VARCHAR(255) NOT NULL;');

        $this->addSql('ALTER TABLE coreshop_carrier CHANGE name identifier VARCHAR(255) DEFAULT NULL;');
        $this->addSql('ALTER TABLE coreshop_carrier_translation CHANGE `label` title VARCHAR(255) DEFAULT NULL;');

        $this->container->get('pimcore.cache.core.handler')->clearTag('doctrine_pimcore_cache');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
