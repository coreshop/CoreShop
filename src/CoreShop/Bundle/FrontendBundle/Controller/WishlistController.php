<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\StorageListManagerInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WishlistController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addItemAction(Request $request)
    {
        $product = $this->get('coreshop.repository.product')->find($request->get('product'));

        if (!$product instanceof ProductInterface) {
            throw new NotFoundHttpException();
        }

        $quantity = intval($request->get('quantity', 1));

        if (!is_int($quantity)) {
            $quantity = 1;
        }

        $this->getWishlistModifier()->addItem($this->getWishlist(), $product, $quantity);

        $this->addFlash('success', 'coreshop.ui.item_added');

        return $this->viewHandler->handle(View::createRouteRedirect('coreshop_wishlist_summary'));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeItemAction(Request $request)
    {
        $product = $this->get('coreshop.repository.product')->find($request->get('product'));

        if (!$product instanceof ProductInterface) {
            throw new NotFoundHttpException();
        }

        $this->addFlash('success', 'coreshop.ui.item_removed');

        $this->getWishlistModifier()->updateItemQuantity($this->getWishlist(), $product, 0);

        return $this->viewHandler->handle(View::createRouteRedirect('coreshop_wishlist_summary'));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function summaryAction(Request $request)
    {
        $view = View::create($this->getWishlist())
            ->setTemplate($this->templateConfigurator->findTemplate('Wishlist/summary.html'))
            ->setTemplateData([
                'whishlist' => $this->getWishlist()
            ]);

        return $this->viewHandler->handle($view);
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
