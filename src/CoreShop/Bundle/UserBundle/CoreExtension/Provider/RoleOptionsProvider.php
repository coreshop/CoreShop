<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\UserBundle\CoreExtension\Provider;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\MultiSelectOptionsProviderInterface;
use Psr\Container\ContainerInterface;

class RoleOptionsProvider implements MultiSelectOptionsProviderInterface
{
    protected array $originalRoles;
    protected array $invalidRoles = [
        'ROLE_PIMCORE_ADMIN',
    ];

    public function __construct()
    {
        $systemRoles = \Pimcore::getKernel()->getContainer()->getParameter('security.role_hierarchy.roles');
        $this->originalRoles = array_diff_key($systemRoles, array_flip($this->invalidRoles));
    }

    /**
     * @param array $context
     * @param Data  $fieldDefinition
     *
     * @return array
     */
    public function getOptions($context, $fieldDefinition)
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
     *
     * @return bool
     */
    public function hasStaticOptions($context, $fieldDefinition)
    {
        return false;
    }

    /**
     * @param array $context
     * @param Data  $fieldDefinition
     *
     * @return mixed
     */
    public function getDefaultValue($context, $fieldDefinition)
    {
        return 'ROLE_USER';
    }
}
