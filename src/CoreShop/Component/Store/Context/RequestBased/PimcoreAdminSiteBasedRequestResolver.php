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
use Pimcore\Http\RequestHelper;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Service;
use Pimcore\Model\Site;
use Symfony\Component\HttpFoundation\Request;

final class PimcoreAdminSiteBasedRequestResolver implements RequestResolverInterface
{
    private StoreRepositoryInterface $storeRepository;
    private RequestHelper $requestHelper;
    private Service $documentService;

    public function __construct(
        StoreRepositoryInterface $storeRepository,
        RequestHelper $requestHelper,
        Service $documentService
    ) {
        $this->storeRepository = $storeRepository;
        $this->requestHelper = $requestHelper;
        $this->documentService = $documentService;
    }

    public function findStore(Request $request): ?StoreInterface
    {
        if ($this->requestHelper->isFrontendRequestByAdmin($request)) {
            /** @psalm-suppress InternalMethod */
            $document = $this->documentService->getNearestDocumentByPath($request->getPathInfo());

            if ($document instanceof Document) {
                do {
                    try {
                        $site = Site::getByRootId($document->getId());

                        if ($site instanceof Site) {
                            return $this->storeRepository->findOneBySite($site->getId());
                        }
                    } catch (\Exception $x) {
                        //Ignore Exception and continue
                    }

                    $document = $document->getParent();
                } while ($document instanceof Document);
            }
        }

        throw new StoreNotFoundException();
    }
}
