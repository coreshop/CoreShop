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
