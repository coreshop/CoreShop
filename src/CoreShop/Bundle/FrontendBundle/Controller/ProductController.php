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

use CoreShop\Component\Product\Model\ProductInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends FrontendController
{
    public function latestAction(Request $request)
    {
        $productRepository = $this->get('coreshop.repository.product');

        return $this->renderTemplate('CoreShopFrontendBundle:Product:_latest.html.twig', [
            'products' => $productRepository->findLatestByStore($this->get('coreshop.context.store')->getStore()),
        ]);
    }

    public function detailAction(Request $request)
    {
        $productRepository = $this->get('coreshop.repository.product');
        $product = $productRepository->find($request->get('product'));

        if (!$product instanceof ProductInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $this->get('coreshop.tracking.manager')->trackPurchasableView($product);

        return $this->renderTemplate('CoreShopFrontendBundle:Product:detail.html.twig', [
            'product' => $product,
        ]);
    }
}
