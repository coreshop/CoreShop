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

namespace CoreShop\Behat\Service;

use CoreShop\Bundle\TestBundle\Service\CookieSetterInterface;
use CoreShop\Component\Store\Model\StoreInterface;

final class StoreContextSetter implements StoreContextSetterInterface
{
    public function __construct(
        private CookieSetterInterface $cookieSetter,
    ) {
    }

    public function setStore(StoreInterface $store): void
    {
        $this->cookieSetter->setCookie('_store_id', (string) $store->getId());
    }
}
