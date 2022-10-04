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

namespace CoreShop\Bundle\CustomerBundle\CoreExtension\Provider;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\MultiSelectOptionsProviderInterface;

class RoleOptionsProvider implements MultiSelectOptionsProviderInterface
{
    protected array $originalRoles;

    protected array $invalidRoles = [
        'ROLE_PIMCORE_ADMIN',
    ];

    public function __construct(
        ) {
        $systemRoles = \Pimcore::getKernel()->getContainer()->getParameter('security.role_hierarchy.roles');
        $this->originalRoles = array_diff_key($systemRoles, array_flip($this->invalidRoles));
    }

    /**
     * @param array $context
     * @param Data  $fieldDefinition
     */
    public function getOptions($context, $fieldDefinition): array
    {
        $roles = [];

        /**
         * Get all unique roles.
         */
        foreach ($this->originalRoles as $originalRole => $inheritedRoles) {
            foreach ($inheritedRoles as $inheritedRole) {
                $roles[] = $inheritedRole;
            }

            $roles[] = $originalRole;
        }

        $result = [];

        foreach (array_unique($roles) as $role) {
            $result[] = ['key' => $role, 'value' => $role];
        }

        return $result;
    }

    /**
     * @param array $context
     * @param Data  $fieldDefinition
     */
    public function hasStaticOptions($context, $fieldDefinition): bool
    {
        return false;
    }

    /**
     * @param array $context
     * @param Data  $fieldDefinition
     */
    public function getDefaultValue($context, $fieldDefinition): string
    {
        return 'ROLE_USER';
    }
}
