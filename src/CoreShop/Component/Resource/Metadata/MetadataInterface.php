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

namespace CoreShop\Component\Resource\Metadata;

interface MetadataInterface
{
    public function getAlias(): string;
    public function getApplicationName(): string;
    public function getName(): string;
    public function getHumanizedName(): string;
    public function getPluralName(): string;
    public function getDriver(): string;
    public function getTemplatesNamespace(): string;
    public function getParameter(string $name);
    public function getParameters(): array;
    public function hasParameter(string $name): bool;
    public function getClass(string $name);
    public function hasClass(string $name): bool;
    public function getServiceId(string $serviceName): string;
    public function getPermissionCode(string $permissionName): string;
}
