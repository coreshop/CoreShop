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
use FOS\RestBundle\View\View;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends FrontendController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function latestAction(Request $request)
    {
        $productRepository = $this->get('coreshop.repository.product');
        $latestProducts = $productRepository->findLatestByStore($this->get('coreshop.context.store')->getStore());

        $view = View::create($latestProducts)
            ->setTemplate($this->templateConfigurator->findTemplate('Product/_latest.html'))
            ->setTemplateData([
                'products' => $latestProducts
            ]);

        return $this->viewHandler->handle($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction(Request $request)
    {
        $product = $this->getProductByRequest($request);

        if (!$product instanceof ProductInterface) {
            throw new NotFoundHttpException('product not found');
        }

        if (!$product->isPublished() || $product->getActive() !== true) {
            throw new NotFoundHttpException('product not found');
        }

        $this->get('coreshop.tracking.manager')->trackPurchasableView($product);

        $view = View::create($product)
            ->setTemplate($this->templateConfigurator->findTemplate('Product/detail.html'))
            ->setTemplateData([
                'product' => $product
            ]);

        return $this->viewHandler->handle($view);
    }

    /**
     * @param Request $request
     * @return DataObject
     */
    private function getProductByRequest(Request $request)
    {
        return $this->get('coreshop.repository.stack.purchasable')->find($request->get('product'));
    }
}
