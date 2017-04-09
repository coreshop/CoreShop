<?php

namespace CoreShop\Component\Store\Context\RequestBased;

use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Pimcore\Model\Site;
use Symfony\Component\HttpFoundation\Request;

final class SiteBasedRequestResolver implements RequestResolverInterface
{
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(StoreRepositoryInterface $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function findStore(Request $request)
    {
        if (Site::isSiteRequest()) {
            return $this->storeRepository->findOneBySite(Site::getCurrentSite()->getId());
        }

        return $this->storeRepository->findStandard();
    }
}
