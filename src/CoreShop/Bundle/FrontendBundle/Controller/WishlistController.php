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

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItem;
use CoreShop\Component\StorageList\StorageListManagerInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WishlistController extends FrontendController
{
    public function addItemAction(Request $request): Response
    {
        $product = $this->get('coreshop.repository.stack.purchasable')->find($request->get('product'));

        if (!$product instanceof PurchasableInterface) {
            $redirect = $request->get('_redirect', $this->generateCoreShopUrl(null, 'coreshop_index'));

            return $this->redirect($redirect);
        }

        $quantity = (int) $request->get('quantity', 1);

        if (!is_int($quantity)) {
            $quantity = 1;
        }

        /**
         * @var StorageListItem $wishlistItem
         */
        $wishlistItem = $this->get('coreshop.factory.wishlist_item')->createNew();
        $wishlistItem->setProduct($product);
        $wishlistItem->setQuantity($quantity);

        $this->getWishlistModifier()->addToList($this->getWishlist(), $wishlistItem);

        $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.item_added'));

        $redirect = $request->get('_redirect', $this->generateCoreShopUrl($this->getWishlist(), 'coreshop_wishlist_summary'));

        return $this->redirect($redirect);
    }

    public function removeItemAction(Request $request): Response
    {
        $product = $this->get('coreshop.repository.stack.purchasable')->find($request->get('product'));

        if (!$product instanceof PurchasableInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.item_removed'));

        foreach ($this->getWishlist()->getItems() as $item) {
            if ($item->getProduct() instanceof $product && $item->getProduct()->getId() === $product->getId()) {
                $this->getWishlistModifier()->removeFromList($this->getWishlist(), $item);

                break;
            }
        }

        return $this->redirectToRoute('coreshop_wishlist_summary');
    }

    public function summaryAction(Request $request): Response
    {
        return $this->render($this->templateConfigurator->findTemplate('Wishlist/summary.html'), [
            'wishlist' => $this->getWishlist(),
        ]);
    }

    protected function getWishlistModifier(): StorageListModifierInterface
    {
        return $this->get('coreshop.wishlist.modifier');
    }

    protected function getWishlist(): StorageListInterface
    {
        return $this->getWishlistManager()->getStorageList();
    }

    protected function getWishlistManager(): StorageListManagerInterface
    {
        return $this->get('coreshop.wishlist.manager');
    }
}
