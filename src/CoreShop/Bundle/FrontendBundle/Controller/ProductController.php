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

use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
use CoreShop\Bundle\ResourceBundle\Pimcore\Repository\StackRepository;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\SEO\SEOPresentationInterface;
use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends FrontendController
{
    public function latestAction(
        ProductRepositoryInterface $productRepository,
        ShopperContextInterface $shopperContext,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        return $this->renderTemplate($templateConfigurator->findTemplate('Product/_latest.html'), [
            'products' => $productRepository->findLatestByStore($shopperContext->getStore()),
        ]);
    }

    public function detailAction(
        Request $request,
        StackRepository $productStackRepository,
        ShopperContextInterface $shopperContext,
        TrackerInterface $tracker,
        SEOPresentationInterface $seo,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $product = $this->getProductByRequest($request, $productStackRepository);

        if (!$product instanceof ProductInterface) {
            throw new NotFoundHttpException('product not found');
        }

        if (!$product->getPublished() || $product->getActive() !== true) {
            throw new NotFoundHttpException('product not found');
        }

        if (!in_array($shopperContext->getStore()->getId(), $product->getStores())) {
            throw new NotFoundHttpException('product not found');
        }

        $tracker->trackProduct($product);
        $seo->updateSeoMetadata($product);

        return $this->renderTemplate($templateConfigurator->findTemplate('Product/detail.html'), [
            'product' => $product,
        ]);
    }

    protected function getProductByRequest(Request $request, StackRepository $productStackRepository)
    {
        return $productStackRepository->find($request->get('product'));
    }
}
