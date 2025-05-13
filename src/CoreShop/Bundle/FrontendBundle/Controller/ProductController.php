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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Pimcore\Repository\StackRepositoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
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
        $productRepository = $this->container->get('coreshop.repository.product');

        return $this->render($this->getTemplateConfigurator()->findTemplate('Product/_latest.html'), [
            'products' => $productRepository->findLatestByStore($this->container->get(StoreContextInterface::class)->getStore()),
        ]);
    }

    public function detailSlugAction(Request $request, ProductInterface $object): Response
    {
        $this->validateProduct($request, $object);

        $this->container->get(SEOPresentationInterface::class)->updateSeoMetadata($object);
        $this->container->get(TrackerInterface::class)->trackProduct($object);

        return $this->render($this->getTemplateConfigurator()->findTemplate('Product/detail.html'), [
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

        $this->container->get(SEOPresentationInterface::class)->updateSeoMetadata($product);
        $this->container->get(TrackerInterface::class)->trackProduct($product);

        return $this->render($this->getTemplateConfigurator()->findTemplate('Product/detail.html'), [
            'product' => $product,
        ]);
    }

    protected function validateProduct(Request $request, ProductInterface $product): void
    {
        $isFrontendRequestByAdmin = false;

        if ($this->container->get(RequestHelper::class)->isFrontendRequestByAdmin($request)) {
            $isFrontendRequestByAdmin = true;
        }

        if ($isFrontendRequestByAdmin === false && (!$product->isPublished() || $product->getActive() !== true)) {
            throw new NotFoundHttpException('product not found');
        }

        if (!in_array($this->container->get(StoreContextInterface::class)->getStore()->getId(), $product->getStores())) {
            throw new NotFoundHttpException('product not found');
        }
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
           'coreshop.repository.product' => ProductRepositoryInterface::class,
            StoreContextInterface::class => StoreContextInterface::class,
            TrackerInterface::class => TrackerInterface::class,
            'coreshop.repository.stack.purchasable' => StackRepositoryInterface::class,
        ]);
    }

    protected function getProductByRequest(Request $request): ?PurchasableInterface
    {
        return $this->container->get('coreshop.repository.stack.purchasable')->find($this->getParameterFromRequest($request, 'product'));
    }
}
