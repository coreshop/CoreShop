<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\SEO\SEOPresentationInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Pimcore\Http\RequestHelper;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function latestAction(Request $request)
    {
        $productRepository = $this->get('coreshop.repository.product');

        return $this->render($this->templateConfigurator->findTemplate('Product/_latest.html'), [
            'products' => $productRepository->findLatestByStore($this->get(StoreContextInterface::class)->getStore()),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction(Request $request)
    {
        $product = $this->getProductByRequest($request);

        $isFrontendRequestByAdmin = false;

        if (!$product instanceof ProductInterface) {
            throw new NotFoundHttpException('product not found');
        }

        if ($this->get(RequestHelper::class)->isFrontendRequestByAdmin($request)) {
            $isFrontendRequestByAdmin = true;
        }

        if ($isFrontendRequestByAdmin === false && (!$product->isPublished() || $product->getActive() !== true)) {
            throw new NotFoundHttpException('product not found');
        }

        if (!in_array($this->get(StoreContextInterface::class)->getStore()->getId(), $product->getStores())) {
            throw new NotFoundHttpException('product not found');
        }

        $this->get(SEOPresentationInterface::class)->updateSeoMetadata($product);
        $this->get(TrackerInterface::class)->trackProduct($product);

        return $this->render($this->templateConfigurator->findTemplate('Product/detail.html'), [
            'product' => $product,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return DataObject\Concrete
     */
    protected function getProductByRequest(Request $request)
    {
        return $this->get('coreshop.repository.stack.purchasable')->find($request->get('product'));
    }
}
