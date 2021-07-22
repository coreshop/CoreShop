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

namespace CoreShop\Component\Resource\Metadata;

interface MetadataInterface
{
    /**
     * @return string
     */
    public function getAlias();

    /**
     * @return string
     */
    public function getApplicationName();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getHumanizedName();

    /**
     * @return string
     */
    public function getPluralName();

    /**
     * @return string
     */
    public function getDriver();

    /**
     * @return string
     */
    public function getTemplatesNamespace();

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
    public function getParameters();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($name);

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
    public function hasClass($name);

    /**
     * @param string $serviceName
     *
     * @return string
     */
    public function getServiceId($serviceName);

    /**
     * @param string $permissionName
     *
     * @return string
     */
    public function getPermissionCode($permissionName);
}
