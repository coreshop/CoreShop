<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Resource\Metadata;

interface MetadataInterface
{
    /**
     * @return string
     */
    public function getAlias(): string;

    /**
     * @return string
     */
    public function getApplicationName(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getHumanizedName(): string;

    /**
     * @return string
     */
    public function getPluralName(): string;

    /**
     * @return string
     */
    public function getDriver(): string;

    /**
     * @return string
     */
    public function getTemplatesNamespace(): string;

    /**
     * @param string $name
     *
     * @return string|array
     *
     * @throws \InvalidArgumentException
     */
    public function getParameter($name);

    /**
     * Return all the metadata parameters.
     *
     * @return array
     */
    public function getParameters(): array;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($name): bool;

    /**
     * @param string $name
     *
     * @return string|array
     *
     * @throws \InvalidArgumentException
     */
    public function getClass($name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasClass($name): bool;

    /**
     * @param string $serviceName
     *
     * @return string
     */
    public function getServiceId($serviceName): string;

    /**
     * @param string $permissionName
     *
     * @return string
     */
    public function getPermissionCode($permissionName): string;
}
