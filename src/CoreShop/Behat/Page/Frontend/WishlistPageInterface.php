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

namespace CoreShop\Behat\Page\Frontend;

use CoreShop\Bundle\TestBundle\Page\Frontend\FrontendPageInterface;

interface WishlistPageInterface extends FrontendPageInterface
{
    public function isEmpty(): bool;

    public function hasItemNamed(string $name): bool;

    public function hasShareWishlistLink(): bool;

    public function getShareWishlistLink(): string;

    public function removeProduct(string $productName): void;
}
