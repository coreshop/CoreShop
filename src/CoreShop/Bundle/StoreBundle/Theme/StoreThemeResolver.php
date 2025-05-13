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

namespace CoreShop\Bundle\StoreBundle\Theme;

use CoreShop\Bundle\ThemeBundle\Service\ThemeNotResolvedException;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;

final class StoreThemeResolver implements ThemeResolverInterface
{
    public function __construct(
        private StoreContextInterface $storeContext,
    ) {
    }

    public function resolveTheme(): string
    {
        try {
            $store = $this->storeContext->getStore();

            if ($theme = $store->getTemplate()) {
                return $theme;
            }
        } catch (StoreNotFoundException $exception) {
            throw new ThemeNotResolvedException($exception);
        }

        throw new ThemeNotResolvedException();
    }
}
