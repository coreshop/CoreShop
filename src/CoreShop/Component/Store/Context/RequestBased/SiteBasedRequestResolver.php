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

namespace CoreShop\Component\Store\Context\RequestBased;

use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Pimcore\Model\Site;
use Symfony\Component\HttpFoundation\Request;

final class SiteBasedRequestResolver implements RequestResolverInterface
{
    private StoreRepositoryInterface $storeRepository;

    public function __construct(StoreRepositoryInterface $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    public function findStore(Request $request): ?StoreInterface
    {
        if (Site::isSiteRequest()) {
            return $this->storeRepository->findOneBySite(Site::getCurrentSite()->getId());
        }

        $defaultStore = $this->storeRepository->findStandard();

        if (null === $defaultStore) {
            throw new StoreNotFoundException();
        }

        return $defaultStore;
    }
}
