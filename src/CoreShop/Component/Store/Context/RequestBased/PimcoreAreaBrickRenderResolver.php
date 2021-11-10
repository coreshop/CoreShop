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
use Pimcore\Model\Document;
use Pimcore\Model\Site;
use Pimcore\Tool\Frontend;
use Symfony\Component\HttpFoundation\Request;

final class PimcoreAreaBrickRenderResolver implements RequestResolverInterface
{
    public function __construct(private StoreRepositoryInterface $storeRepository)
    {
    }

    public function findStore(Request $request): ?StoreInterface
    {
        if ($request->attributes->get('_route') === 'pimcore_admin_document_page_areabrick-render-index-editmode') {
            /** @psalm-suppress InternalMethod */
            $document = Document::getById($request->request->get('documentId'));

            if ($document) {
                $site = Frontend::getSiteForDocument($document);

                if ($site instanceof Site) {
                    return $this->storeRepository->findOneBySite($site->getId());
                }
            }
        }

        throw new StoreNotFoundException();
    }
}
