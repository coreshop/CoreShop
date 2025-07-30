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

namespace CoreShop\Bundle\TestBundle\Page\Frontend;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface FrontendPageInterface extends SymfonyPageInterface
{
    public function isOpenWithUri(string $uri): bool;

    public function tryToOpenWithUri(string $uri): void;
}
