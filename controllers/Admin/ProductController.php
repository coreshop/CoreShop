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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
use Pimcore\Controller\Action\Admin;
use Pimcore\Tool as PimTool;

class CoreShop_Admin_ProductController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array('list');
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            //$this->checkPermission("coreshop_permission_priceRules");
            //TODO
        }
    }

    public function getProductsAction()
    {
        $list = \CoreShop\Model\Product::getList();
        $list->setLimit($this->getParam('limit', 30));
        $list->setOffset($this->getParam('page', 1) - 1);

        if ($this->getParam('filter', null)) {
            $conditionFilters[] = \Pimcore\Model\Object\Service::getFilterCondition($this->getParam('filter'), \Pimcore\Model\Object\ClassDefinition::getByName('CoreShopProduct'));
            if (count($conditionFilters) > 0 && $conditionFilters[0] !== '(())') {
                $list->setCondition(implode(' AND ', $conditionFilters));
            }
        }

        $sortingSettings = \Pimcore\Admin\Helper\QueryParams::extractSortingSettings($this->getAllParams());

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
        $jsonProducts = array();

        foreach ($products as $product) {
            $jsonProducts[] = $this->prepareProduct($product);
        }

        $this->_helper->json(array('success' => true, 'data' => $jsonProducts, 'count' => count($jsonProducts), 'total' => $list->getTotalCount()));
    }

    protected function prepareProduct(\CoreShop\Model\Product $product)
    {
        $element = array(
            'o_id' => $product->getId(),
            'name' => $product->getName(),
            'quantity' => $product->getQuantity(),
            'price' => $product->getPrice(),
        );

        return $element;
    }
}
