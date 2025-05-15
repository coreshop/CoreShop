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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

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
