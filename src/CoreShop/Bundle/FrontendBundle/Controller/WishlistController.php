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

use CoreShop\Bundle\WishlistBundle\DTO\AddToWishlistInterface;
use CoreShop\Bundle\WishlistBundle\Factory\AddToWishlistFactoryInterface;
use CoreShop\Bundle\WishlistBundle\Form\Type\AddToWishlistType;
use CoreShop\Component\StorageList\StorageListManagerInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use CoreShop\Component\Wishlist\Context\WishlistContextInterface;
use CoreShop\Component\Wishlist\Manager\WishlistManagerInterface;
use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Model\WishlistItemInterface;
use CoreShop\Component\Wishlist\Model\WishlistProductInterface;
use CoreShop\Component\Wishlist\Wishlist\WishlistModifierInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WishlistController extends FrontendController
{
    public function addItemAction(Request $request): Response
    {
        $redirect = $this->getParameterFromRequest($request, '_redirect', $this->generateUrl('coreshop_index'));
        $product = $this->get('coreshop.repository.stack.wishlist_product')->find($this->getParameterFromRequest($request, 'product'));
        $wishlist = $this->get(WishlistContextInterface::class)->getWishlist();

        if (!$wishlist) {
            $wishlist = $this->get('coreshop.factory.wishlist')->createNew();
        }

        if (!$product instanceof WishlistProductInterface) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => false,
                ]);
            }

            return $this->redirect($redirect);
        }

        $item = $this->get('coreshop.factory.wishlist_item')->createWithWishlist($product);

        $addToWishlist = $this->createAddToWishlist($wishlist, $item);

        $form = $this->get('form.factory')->createNamed('coreshop-' . $product->getId(), AddToWishlistType::class, $addToWishlist);

        if ($request->isMethod('POST')) {
            $redirect = $this->getParameterFromRequest($request, '_redirect', $this->generateUrl('coreshop_wishlist_summary'));

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /**
                 * @var AddToWishlistInterface $addToWishlist
                 */
                $addToWishlist = $form->getData();

                $this->getWishlistModifier()->addToList($addToWishlist->getWishlist(), $addToWishlist->getWishlistItem());
                $this->getWishlistManager()->persist($wishlist);

                $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.item_added'));

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse([
                        'success' => true,
                    ]);
                }

                return $this->redirect($redirect);
            }

            foreach ($form->getErrors(true, true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => false,
                    'errors' => array_map(static function (FormError $error) {
                        return $error->getMessage();
                    }, iterator_to_array($form->getErrors(true))),
                ]);
            }

            return $this->redirect($redirect);
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => false,
            ]);
        }

        return $this->render(
            $this->getParameterFromRequest($request, 'template', $this->templateConfigurator->findTemplate('Product/_addToWishlist.html')),
            [
                'form' => $form->createView(),
                'product' => $product,
            ]
        );
    }

    public function summaryAction(Request $request): Response
    {
        return $this->render($this->templateConfigurator->findTemplate('Wishlist/summary.html'), [
            'wishlist' => $this->get(WishlistContextInterface::class)->getWishlist()
        ]);
    }

    protected function createAddToWishlist(WishlistInterface $wishlist, WishlistItemInterface $wishlistItem): AddToWishlistInterface
    {
        return $this->get(AddToWishlistFactoryInterface::class)->createWithWishlistAndWishlistItem($wishlist, $wishlistItem);
    }

    protected function getWishlistModifier(): StorageListModifierInterface
    {
        return $this->get(WishlistModifierInterface::class);
    }

    protected function getWishlistManager(): WishlistManagerInterface
    {
        return $this->get(WishlistManagerInterface::class);
    }
}
