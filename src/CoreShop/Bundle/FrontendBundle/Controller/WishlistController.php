<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\StorageListManagerInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use Symfony\Component\HttpFoundation\Request;

class WishlistController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addItemAction(Request $request)
    {
        $product = $this->get('coreshop.repository.product')->find($request->get('product'));

        if (!$product instanceof ProductInterface) {
            $redirect = $request->get('_redirect', $this->generateCoreShopUrl(null, 'coreshop_index'));

            return $this->redirect($redirect);
        }

        $quantity = intval($request->get('quantity', 1));

        if (!is_int($quantity)) {
            $quantity = 1;
        }

        $this->getWishlistModifier()->addItem($this->getWishlist(), $product, $quantity);

        $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.item_added'));

        $redirect = $request->get('_redirect', $this->generateCoreShopUrl($this->getWishlist(), 'coreshop_wishlist_summary'));

        return $this->redirect($redirect);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeItemAction(Request $request)
    {
        $product = $this->get('coreshop.repository.product')->find($request->get('product'));

        if (!$product instanceof ProductInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.item_removed'));

        $this->getWishlistModifier()->updateItemQuantity($this->getWishlist(), $product, 0);

        return $this->redirectToRoute('coreshop_wishlist_summary');
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function summaryAction(Request $request)
    {
        return $this->renderTemplate($this->templateConfigurator->findTemplate('Wishlist/summary.html'), [
            'wishlist' => $this->getWishlist(),
        ]);
    }

    /**
     * @return StorageListModifierInterface
     */
    protected function getWishlistModifier()
    {
        return $this->get('coreshop.wishlist.modifier');
    }

    /**
     * @return StorageListInterface
     */
    protected function getWishlist()
    {
        return $this->getWishlistManager()->getStorageList();
    }

    /**
     * @return StorageListManagerInterface
     */
    protected function getWishlistManager()
    {
        return $this->get('coreshop.wishlist.manager');
    }
}
