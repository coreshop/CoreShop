<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\SEO\SEOPresentationInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Pimcore\Http\RequestHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends FrontendController
{
    public function latestAction(Request $request): Response
    {
        $productRepository = $this->get('coreshop.repository.product');

        return $this->render($this->templateConfigurator->findTemplate('Product/_latest.html'), [
            'products' => $productRepository->findLatestByStore($this->get(StoreContextInterface::class)->getStore()),
        ]);
    }

    public function detailSlugAction(Request $request, ProductInterface $object): Response
    {
        $this->validateProduct($request, $object);

        $this->get(SEOPresentationInterface::class)->updateSeoMetadata($object);
        $this->get(TrackerInterface::class)->trackProduct($object);

        return $this->render($this->templateConfigurator->findTemplate('Product/detail.html'), [
            'product' => $object,
        ]);
    }

    public function detailAction(Request $request): Response
    {
        $product = $this->getProductByRequest($request);

        if (!$product instanceof ProductInterface) {
            throw new NotFoundHttpException('product not found');
        }

        $this->validateProduct($request, $product);

        $this->get(SEOPresentationInterface::class)->updateSeoMetadata($product);
        $this->get(TrackerInterface::class)->trackProduct($product);

        return $this->render($this->templateConfigurator->findTemplate('Product/detail.html'), [
            'product' => $product,
        ]);
    }

    protected function validateProduct(Request $request, ProductInterface $product): void
    {
        $isFrontendRequestByAdmin = false;

        if ($this->get(RequestHelper::class)->isFrontendRequestByAdmin($request)) {
            $isFrontendRequestByAdmin = true;
        }

        if ($isFrontendRequestByAdmin === false && (!$product->isPublished() || $product->getActive() !== true)) {
            throw new NotFoundHttpException('product not found');
        }

        if (!in_array($this->get(StoreContextInterface::class)->getStore()->getId(), $product->getStores())) {
            throw new NotFoundHttpException('product not found');
        }
    }

    protected function getProductByRequest(Request $request): ?PurchasableInterface
    {
        return $this->get('coreshop.repository.stack.purchasable')->find($this->getParameterFromRequest($request, 'product'));
    }
}
