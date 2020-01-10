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

namespace CoreShop\Bundle\ProductBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\Pimcore\Repository\StackRepository;
use CoreShop\Component\Product\Model\ProductInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductUnitDefinitionsController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function productUnitDefinitionsListAction(Request $request)
    {
        $definitions = [];

        /** @var StackRepository $repository */
        $repository = $this->get('coreshop.repository.stack.product');

        /** @var ProductInterface $product */
        $product = $repository->find($request->get('productId'));

        if ($product instanceof ProductInterface) {
            $definitions = $this->getUnitDefinitionsForProduct($product, 'all');
        }

        return $this->viewHandler->handle($definitions);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function productAdditionalUnitDefinitionsListAction(Request $request)
    {
        $definitions = [];

        /** @var StackRepository $repository */
        $repository = $this->get('coreshop.repository.stack.product');

        /** @var ProductInterface $product */
        $product = $repository->find($request->get('productId'));

        if ($product instanceof ProductInterface) {
            $definitions = $this->getUnitDefinitionsForProduct($product, 'additional');
        }

        return $this->viewHandler->handle($definitions);
    }

    /**
     * @param ProductInterface $product
     * @param string           $type
     *
     * @return array
     */
    protected function getUnitDefinitionsForProduct(ProductInterface $product, string $type = 'all')
    {
        $definitions = [];

        if ($product->hasUnitDefinitions()) {
            $productUnitDefinitions = $product->getUnitDefinitions();
            $definitions = $type === 'additional'
                ? $productUnitDefinitions->getAdditionalUnitDefinitions()
                : $productUnitDefinitions->getUnitDefinitions();
        } else {
            if ($product instanceof Concrete && $product->getClass()->getAllowInherit() && $product->getParent() instanceof ProductInterface) {
                $definitions = $this->getUnitDefinitionsForProduct($product->getParent(), $type);
            }
        }

        return $definitions;
    }
}
