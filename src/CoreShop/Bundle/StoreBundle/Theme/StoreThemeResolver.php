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

declare(strict_types=1);

namespace CoreShop\Bundle\StoreBundle\Theme;

use CoreShop\Bundle\ThemeBundle\Service\ThemeNotResolvedException;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;

final class StoreThemeResolver implements ThemeResolverInterface
{
    private StoreContextInterface $storeContext;

    public function __construct(StoreContextInterface $storeContext)
    {
        $this->storeContext = $storeContext;
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
