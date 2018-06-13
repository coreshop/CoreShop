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

use CoreShop\Bundle\TrackingBundle\Manager\TrackingManagerInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\SEO\SEOPresentationInterface;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends FrontendController
{
    /**
     * @var TrackingManagerInterface
     */
    protected $trackingManager;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function latestAction(Request $request)
    {
        $productRepository = $this->get('coreshop.repository.product');

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Product/_latest.html'), [
            'products' => $productRepository->findLatestByStore($this->get('coreshop.context.store')->getStore()),
        ]);
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
        
        if (!in_array($this->get('coreshop.context.store')->getStore()->getId(), $product->getStores())) {
            throw new NotFoundHttpException('product not found');
        }

        $this->seoPresentation->updateSeoMetadata($product);
        $this->trackingManager->trackPurchasableView($product);

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Product/detail.html'), [
            'product' => $product,
        ]);
    }

    /**
     * @param Request $request
     * @return DataObject
     */
    private function getProductByRequest(Request $request)
    {
        return $this->get('coreshop.repository.stack.purchasable')->find($request->get('product'));
    }

    /**
     * @param TrackingManagerInterface $trackingManager
     */
    public function setTrackingManager(TrackingManagerInterface $trackingManager)
    {
        $this->trackingManager = $trackingManager;
    }
}
