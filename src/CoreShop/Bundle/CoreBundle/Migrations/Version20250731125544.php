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

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Bundle\ResourceBundle\ResourcePermission;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Model\User\Permission\Definition;

final class Version20250731125544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migrate CoreShop Permissions to new structure';
    }

    public function up(Schema $schema): void
    {
        $permissions = [
            'coreshop_permission_cart_price_rule',
            'coreshop_permission_product_price_rule',
            'coreshop_permission_product_unit',
            'coreshop_permission_country',
            'coreshop_permission_state',
            'coreshop_permission_zone',
            'coreshop_permission_currency',
            'coreshop_permission_exchange_rate',
            'coreshop_permission_tax_rate',
            'coreshop_permission_tax_rule_group',
            'coreshop_permission_store',
            'coreshop_permission_index',
            'coreshop_permission_filter',
            'coreshop_permission_carrier',
            'coreshop_permission_shipping_rule',
            'coreshop_permission_payment_provider',
            'coreshop_permission_payment_provider_rule',
            'coreshop_permission_notification',
        ];

        foreach ($permissions as $permissionKey) {
            foreach (ResourcePermission::getAllPermissions() as $resourcePermission) {
                $subPermissionKey = sprintf('%s_%s', $permissionKey, $resourcePermission);

                $permissionDefinition = Definition::getByKey($subPermissionKey);

                if (!$permissionDefinition instanceof Definition) {
                    $this->addSql('INSERT INTO users_permission_definitions (`key`, `category`) VALUES (?, ?)', [
                        $subPermissionKey,
                        sprintf('coreshop_permission_group_%s', 'coreshop'),
                    ]);
                }
            }
        }

        $users = new \Pimcore\Model\User\Listing();
        $users = $users->getUsers();

        foreach ($users as $user) {
            $newUserPermissions = [];
            $changed = false;

            foreach ($user->getPermissions() as $permission) {
                if (!in_array($permission, $permissions)) {
                    $newUserPermissions[] = $permission;

                    continue;
                }

                foreach (ResourcePermission::getAllPermissions() as $resourcePermission) {
                    $changed = true;
                    $newUserPermissions[] = sprintf('%s_%s', $permission, $resourcePermission);
                }
            }

            if ($changed) {
                $user->setPermissions($newUserPermissions);
                $user->save();
            }
        }

        foreach ($permissions as $permissionKey) {
            $this->addSql('DELETE FROM users_permission_definitions WHERE `key` = ?', [$permissionKey]);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
