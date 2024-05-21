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

use CoreShop\Bundle\ResourceBundle\Installer\PimcoreRoutesInstaller;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20240117102103 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return 'Update configuration of static routes by adding a new `coreshop_payment_token` route';
    }

    public function up(Schema $schema): void
    {
        $this->container->get(PimcoreRoutesInstaller::class)->installResources(new NullOutput(), 'coreshop');
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('settings_store')) {
            $this->addSql("DELETE FROM settings_store WHERE id='coreshop_payment_token'");
        }
    }
}
