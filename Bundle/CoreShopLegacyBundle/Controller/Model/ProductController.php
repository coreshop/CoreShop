<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Controller\Model;

use CoreShop\Bundle\CoreShopLegacyBundle\Controller\Admin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 *
 * @Route("/product")
 */
class ProductController extends Admin\AdminController
{
    public function init()
    {
        parent::init();

        /**
         * TODO: implement permission
         */
    }

    /**
     * @Route("/get-products")
     */
    public function getProductsAction(Request $request)
    {
        $list = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product::getList();
        $list->setLimit($request->get('limit', 30));
        $list->setOffset($request->get('page', 1) - 1);

        if ($request->get('filter', null)) {
            $conditionFilters = [];
            $conditionFilters[] = \Pimcore\Model\Object\Service::getFilterCondition($request->get('filter'), \Pimcore\Model\Object\ClassDefinition::getByName('CoreShopProduct'));
            if (count($conditionFilters) > 0 && $conditionFilters[0] !== '(())') {
                $list->setCondition(implode(' AND ', $conditionFilters));
            }
        }

        $sortingSettings = \Pimcore\Admin\Helper\QueryParams::extractSortingSettings($request->request->all());

        $order = 'DESC';
        $orderKey = 'o_id';

        if ($sortingSettings['order']) {
            $order = $sortingSettings['order'];
        }
        if (strlen($sortingSettings['orderKey']) > 0) {
            $orderKey = $sortingSettings['orderKey'];
        }

        $list->setOrder($order);
        $list->setOrderKey($orderKey);

        $products = $list->load();
        $jsonProducts = [];

        foreach ($products as $product) {
            $jsonProducts[] = $this->prepareProduct($product);
        }

        return $this->json(['success' => true, 'data' => $jsonProducts, 'count' => count($jsonProducts), 'total' => $list->getTotalCount()]);
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product $product
     * @return array
     */
    protected function prepareProduct(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Product $product)
    {
        $element = [
            'o_id' => $product->getId(),
            'name' => $product->getName(),
            'quantity' => $product->getQuantity(),
            'price' => $product->getPrice(),
            'shops' => $product->getShops()
        ];

        return $element;
    }
}
