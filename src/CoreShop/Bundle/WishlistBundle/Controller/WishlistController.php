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

namespace CoreShop\Bundle\WishlistBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Repository\WishlistRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\DataLoader;
use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use JMS\Serializer\ArrayTransformerInterface;
use Pimcore\Bundle\AdminBundle\Helper\GridHelperService;
use Pimcore\Bundle\AdminBundle\Helper\QueryParams;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Listing;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class WishlistController extends PimcoreController
{
    protected EventDispatcherInterface $eventDispatcher;

    protected NoteServiceInterface $objectNoteService;

    protected ArrayTransformerInterface $serializer;

    public function getFolderConfigurationAction(Request $request): Response
    {
        $this->isGrantedOr403();

        $name = null;
        $folderId = null;

        $wishlistClassId = (string)$this->container->getParameter('coreshop.model.wishlist.pimcore_class_name');
        $folderPath = 'coreshop/wishlists';
        $wishlistClassDefinition = DataObject\ClassDefinition::getByName($wishlistClassId);

        $folder = DataObject::getByPath('/' . $folderPath);

        if ($folder instanceof DataObject\Folder) {
            $folderId = $folder->getId();
        }

        if ($wishlistClassDefinition instanceof DataObject\ClassDefinition) {
            $name = $wishlistClassDefinition->getName();
        }

        return $this->viewHandler->handle(['success' => true, 'className' => $name, 'folderId' => $folderId]);
    }

    public function listAction(Request $request, WishlistRepositoryInterface $wishlistRepository): Response
    {
        $this->isGrantedOr403();

        $list = $wishlistRepository->getList();
        $list->setLimit($this->getParameterFromRequest($request, 'limit', 30));
        $list->setOffset($this->getParameterFromRequest($request, 'page', 1) - 1);

        if ($this->getParameterFromRequest($request, 'filter')) {
            /** @psalm-suppress InternalClass */
            $gridHelper = new GridHelperService();

            $conditionFilters = [];
            /** @psalm-suppress InternalMethod */
            $conditionFilters[] = $gridHelper->getFilterCondition(
                $this->getParameterFromRequest($request,'filter'),
                DataObject\ClassDefinition::getByName((string)$this->container->getParameter('coreshop.model.wishlist.pimcore_class_name'))
            );
            if (count($conditionFilters) > 0 && $conditionFilters[0] !== '(())') {
                $list->setCondition(implode(' AND ', $conditionFilters));
            }
        }

        /** @psalm-suppress InternalClass, InternalMethod */
        $sortingSettings = QueryParams::extractSortingSettings($request->request->all());

        $order = 'DESC';
        $orderKey = 'o_creationDate';

        if ($sortingSettings['order']) {
            $order = $sortingSettings['order'];
        }
        if ($sortingSettings['orderKey'] !== '') {
            $orderKey = $sortingSettings['orderKey'];
        }

        $list->setOrder($order);
        $list->setOrderKey($orderKey);

        /**
         * @var Listing $list
         */
        $wishlists = $list->getData();
        $jsonSales = [];

        foreach ($wishlists as $wishlist) {
            $jsonSales[] = $this->prepareWishlist($wishlist);
        }

        return $this->viewHandler->handle([
            'success' => true,
            'data' => $jsonSales,
            'count' => count($jsonSales),
            'total' => $list->getTotalCount(),
        ]);
    }

    public function detailAction(Request $request, WishlistRepositoryInterface $wishlistRepository): Response
    {
        $this->isGrantedOr403();

        $wishlistId = $this->getParameterFromRequest($request,'id');
        $wishlist = $wishlistRepository->find($wishlistId);

        if (!$wishlist instanceof WishlistInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Wishlist with ID '$wishlistId' not found"]);
        }

        $jsonSale = $this->getDetails($wishlist);

        return $this->viewHandler->handle(['success' => true, 'sale' => $jsonSale]);
    }

    public function findWishlistAction(Request $request, WishlistRepositoryInterface $orderRepository): Response
    {
        $this->isGrantedOr403();

        $name = $this->getParameterFromRequest($request,'name');

        if ($name) {
            $list = $orderRepository->getList();
            $list->setCondition('name = ? OR o_id = ?', [$name, $name]);

            $wishlists = $list->getData();

            if (count($wishlists) > 0) {
                return $this->viewHandler->handle(['success' => true, 'id' => $wishlists[0]->getId()]);
            }
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    protected function getDetails(WishlistInterface $wishlist): array
    {
        $jsonWishlist = $this->serializer->toArray($wishlist);

        $jsonWishlist['o_id'] = $wishlist->getId();
        $jsonWishlist['name'] = $wishlist->getName();
        $jsonWishlist['store'] = $wishlist->getStore() instanceof StoreInterface ? $this->getStore($wishlist->getStore()) : null;

        if (!isset($jsonWishlist['items'])) {
            $jsonWishlist['items'] = [];
        }

        return [
            'wishlist' => $wishlist,
            'json' => $jsonWishlist
        ];
    }

    protected function prepareWishlist(WishlistInterface $wishlist): array
    {
        $date = $wishlist->getCreationDate()->timestamp;

        $element = [
            'o_id' => $wishlist->getId(),
            'date' => $date,
            'name' => $wishlist->getName(),
            'customerName' => $wishlist->getCustomer() instanceof CustomerInterface ? $wishlist->getCustomer()->getFirstname() . ' ' . $wishlist->getCustomer()->getLastname() : '',
            'customerEmail' => $wishlist->getCustomer() instanceof CustomerInterface ? $wishlist->getCustomer()->getEmail() : '',
            'store' => $wishlist->getStore() instanceof StoreInterface ? $wishlist->getStore()->getId() : null,
        ];

        return $element;
    }

    protected function getStore(StoreInterface $store): array
    {
        return [
            'id' => $store->getId(),
            'name' => $store->getName(),
        ];
    }

    protected function getDataForObject($data): array
    {
        if ($data instanceof DataObject\Concrete) {
            $dataLoader = new DataLoader();

            return $dataLoader->getDataForObject($data);
        }

        return [];
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setObjectNoteService(NoteServiceInterface $objectNoteService): void
    {
        $this->objectNoteService = $objectNoteService;
    }

    public function setSerializer(ArrayTransformerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }
}
