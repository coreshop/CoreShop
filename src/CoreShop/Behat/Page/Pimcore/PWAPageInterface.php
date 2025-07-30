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

namespace CoreShop\Behat\Page\Pimcore;

interface PWAPageInterface extends PimcorePageInterface
{
    public function waitTillLoaded(): void;

    public function hasLogoutButton(): bool;

    public function hasPimcoreTabWithId(string $id): bool;

    public function openResource(string $application, string $resource): void;
}
