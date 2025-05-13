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

namespace CoreShop\Bundle\StoreBundle\Context\Debug;

use Symfony\Component\HttpFoundation\Request;

final class DebugStoreProvider implements DebugStoreProviderInterface
{
    public function getStoreId(Request $request): ?string
    {
        $queryStoreId = $request->query->get('_store_id');
        if (is_string($queryStoreId) && $queryStoreId !== '') {
            return $queryStoreId;
        }

        $cookiesStoreId = $request->cookies->get('_store_id');
        if (is_string($cookiesStoreId) && $cookiesStoreId !== '') {
            return $cookiesStoreId;
        }

        return null;
    }
}
