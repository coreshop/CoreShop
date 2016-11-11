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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Controller\Action;

/**
 * Class CoreShop_ProductController
 */
class CoreShop_ProductController extends Action
{
    public function detailAction()
    {
        $id = $this->getParam('product');
        $product = \CoreShop\Model\Product::getById($id);
        $this->view->contacts = \CoreShop\Model\Messaging\Contact::getList()->load();

        if ($product instanceof \CoreShop\Model\Product) {
            if (!in_array(\CoreShop\Model\Shop::getShop()->getId(), $product->getShops())) {
                throw new CoreShop\Exception(sprintf('Product (%s) not valid for shop (%s)', $id, \CoreShop\Model\Shop::getShop()->getId()));
            }

            $this->view->product = $product;
            $this->view->similarProducts = array();
            
            $this->view->seo = array(
                'image' => $product->getImage(),
                'description' => $product->getMetaDescription() ? $product->getMetaDescription() : $product->getShortDescription(),
            );

            if (count($product->getCategories()) > 0) {
                $mainCategory = $product->getCategories()[0];

                if ($mainCategory->getFilterDefinition() instanceof \CoreShop\Model\Product\Filter) {
                    $this->view->similarProducts = $this->getSimilarProducts($product, $mainCategory->getFilterDefinition());
                }
            }

            if ($this->getRequest()->isPost()) {
                $params = $this->getAllParams();

                $result = \CoreShop\Model\Messaging\Service::handleRequestAndCreateThread($params, $this->language);

                if ($result['success']) {
                    $this->view->success = true;
                } else {
                    $this->view->success = false;
                    $this->view->error = $this->view->translate($result['message']);
                }
            }

            $this->view->headTitle($product->getMetaTitle() ? $product->getMetaTitle() : $product->getName());

            \CoreShop\Tracking\TrackingManager::getInstance()->trackProductView($this->view->product);
        } else {
            throw new CoreShop\Exception(sprintf('Product with id "%s" not found', $id));
        }
    }

    public function indexAction()
    {
        $this->view->headTitle('Home');
    }

    public function previewAction()
    {
        $id = $this->getParam('id');
        $product = \CoreShop\Model\Product::getById($id);

        $this->disableLayout();

        if ($product instanceof \CoreShop\Model\Product) {
            $this->view->product = $product;

            \CoreShop\Tracking\TrackingManager::getInstance()->trackProductView($this->view->product);
        } else {
            throw new \CoreShop\Exception(sprintf('Product with id %s not found', $id));
        }
    }

    public function listAction()
    {
        $listModeDefault = \CoreShop\Model\Configuration::get("SYSTEM.CATEGORY.LIST.MODE");
        $gridPerPageAllowed = \CoreShop\Model\Configuration::get("SYSTEM.CATEGORY.GRID.PER_PAGE");
        $gridPerPageDefault = \CoreShop\Model\Configuration::get("SYSTEM.CATEGORY.GRID.PER_PAGE_DEFAULT");
        $listPerPageAllowed = \CoreShop\Model\Configuration::get("SYSTEM.CATEGORY.LIST.PER_PAGE");
        $listPerPageDefault = \CoreShop\Model\Configuration::get("SYSTEM.CATEGORY.LIST.PER_PAGE_DEFAULT");
        
        $id = $this->getParam('category');
        $page = $this->getParam('page', 0);
        $sort = $this->getParam('sort', 'NAMEA');
        $type = $this->getParam('type', $listModeDefault);

        $defaultPerPage = $type === "list" ? $listPerPageDefault : $gridPerPageDefault;
        $allowedPerPage = $type === "list" ? $listPerPageAllowed : $gridPerPageAllowed;

        $perPage = $this->getParam('perPage', $defaultPerPage);

        if(!in_array($perPage, $allowedPerPage)) {
            $perPage = $defaultPerPage;
        }

        $category = \CoreShop\Model\Category::getById($id);

        if ($category instanceof \CoreShop\Model\Category) {
            if (!in_array(\CoreShop\Model\Shop::getShop()->getId(), $category->getShops())) {
                throw new CoreShop\Exception(sprintf('Category (%s) not valid for shop (%s)', $id, \CoreShop\Model\Shop::getShop()->getId()));
            }
            
            if ($category->getFilterDefinition() instanceof \CoreShop\Model\Product\Filter) {
                $index = $category->getFilterDefinition()->getIndex();
                $indexService = \CoreShop\IndexService::getIndexService()->getWorker($index->getName());

                $list = $indexService->getProductList();
                $list->setVariantMode(\CoreShop\Model\Product\Listing::VARIANT_MODE_HIDE);
                $list->setCategory($category);

                $this->view->currentFilter = \CoreShop\Model\Product\Filter\Helper::setupProductList($list, $this->getAllParams(), $category->getFilterDefinition(), new \CoreShop\Model\Product\Filter\Service());

                if($category->getFilterDefinition()->getUseShopPagingSettings()) {
                    $list->setLimit($perPage);
                }

                $this->view->filter = $category->getFilterDefinition();
                $this->view->list = $list;
                $this->view->params = $this->getAllParams();

                $paginator = Zend_Paginator::factory($list);
                $paginator->setCurrentPageNumber($page);
                $paginator->setItemCountPerPage($list->getLimit());
                $paginator->setPageRange(10);

                $this->view->paginator = $paginator;
            } else {
                $this->view->paginator = $category->getProductsPaging($page, $perPage, $this->parseSorting($sort), true);
            }

            foreach ($this->view->paginator as $product) {
                \CoreShop\Tracking\TrackingManager::getInstance()->trackProductImpression($product);
            }

            $this->view->category = $category;
            $this->view->page = $page;
            $this->view->sort = $sort;
            $this->view->perPage = $perPage;
            $this->view->type = $type;
            $this->view->perPageAllowed = $allowedPerPage;

            $this->view->seo = array(
                'image' => $category->getImage(),
                'description' => $category->getMetaDescription() ? $category->getMetaDescription() : $category->getDescription(),
            );

            $this->view->headTitle($category->getMetaTitle() ? $category->getMetaTitle() : $category->getName());
        } else {
            throw new CoreShop\Exception(sprintf('Category with id "%s" not found', $id));
        }
    }

    /**
     * @param $sortString
     * @return array
     */
    protected function parseSorting($sortString)
    {
        $allowed = array('name', 'price');
        $sort = array(
            'name' => 'name',
            'direction' => 'asc',
        );

        $sortString = explode('_', $sortString);

        if (count($sortString) < 2) {
            return $sort;
        }

        $name = strtolower($sortString[0]);
        $direction = strtolower($sortString[1]);

        if (in_array($name, $allowed) && in_array($direction, array('desc', 'asc'))) {
            return array(
                'name' => $name,
                'direction' => $direction,
            );
        }

        return $sort;
    }

    /**
     * get similar products based on filter
     *
     * @param \CoreShop\Model\Product $product
     * @param \CoreShop\Model\Product\Filter $filter
     * @return array|\CoreShop\Model\Product[]
     */
    protected function getSimilarProducts(\CoreShop\Model\Product $product, \CoreShop\Model\Product\Filter $filter)
    {
        $index = $filter->getIndex();

        if (!$index instanceof CoreShop\Model\Index) {
            return array();
        }

        $indexService = \CoreShop\IndexService::getIndexService()->getWorker($index->getName());

        $productList = $indexService->getProductList();
        $productList->setVariantMode(\CoreShop\Model\Product\Listing::VARIANT_MODE_INCLUDE_PARENT_OBJECT);
        $similarityFields = $filter->getSimilarities();

        if (is_array($similarityFields) && count($similarityFields) > 0) {
            $statement = $productList->buildSimilarityOrderBy($filter->getSimilarities(), $product->getId());
        }

        if (!empty($statement)) {
            $productList->setLimit(2);
            $productList->setOrder("ASC");
            $productList->addCondition(\CoreShop\IndexService\Condition::notMatch("o_virtualProductId", $product->getId()), "o_virtualProductId");
            
            /*if($filterDefinition->getCrossSellingCategory()) {
                $productList->setCategory($filterDefinition->getCrossSellingCategory());
            }*/
            $productList->setOrderKey($statement);

            return $productList->load();
        }

        return array();
    }
}
