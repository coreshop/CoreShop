<?php
/**
 * CoreShop
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

use CoreShop\Controller\Action;
use Pimcore\Model\Object\CoreShopProduct;
use Pimcore\Model\Object\CoreShopCategory;

class CoreShop_ProductController extends Action
{
    
    public function detailAction()
    {
        $id = $this->getParam("product");
        $product = \CoreShop\Model\Product::getById($id);
        $this->view->contacts = \CoreShop\Model\Messaging\Contact::getList()->load();

        if ($product instanceof \CoreShop\Model\Product) {
            $this->view->product = $product;
            
            $this->view->seo = array(
                "image" => $product->getImage(),
                "description" => $product->getMetaDescription() ? $product->getMetaDescription() : $product->getShortDescription()
            );

            if($this->getRequest()->isPost()) {
                $params = $this->getAllParams();

                $result = \CoreShop\Model\Messaging\Service::handleRequestAndCreateThread($params, $this->language);

                if($result['success']) {
                    $this->view->success = true;
                }
                else {
                    $this->view->success = false;
                    $this->view->error = $this->view->translate($result['message']);
                }
            }


            $this->view->headTitle($product->getMetaTitle() ? $product->getMetaTitle() : $product->getName());
        } else {
            throw new CoreShop\Exception(sprintf('Product with id "%s" not found', $id));
        }
    }
    
    public function indexAction()
    {
        $this->view->headTitle("Home");
    }

    public function previewAction()
    {
        $id = $this->getParam("id");
        $product = \CoreShop\Model\Product::getById($id);

        $this->disableLayout();

        if ($product instanceof \CoreShop\Model\Product) {
            $this->view->product = $product;
        } else {
            throw new \Exception(sprintf("Product with id %s not found", $id));
        }
    }

    public function listAction()
    {
        $id = $this->getParam("category");
        $page = $this->getParam("page", 0);
        $sort = $this->getParam("sort", "NAMEA");
        $perPage = $this->getParam("perPage", 12);
        $type = $this->getParam("type", "list");

        $category = \CoreShop\Model\Category::getById($id);

        if ($category instanceof \CoreShop\Model\Category) {
            if ($category->getFilterDefinition() instanceof \CoreShop\Model\Product\Filter) {
                $index = $category->getFilterDefinition()->getIndex();
                $indexService = \CoreShop\IndexService::getIndexService()->getWorker($index->getName());

                $list = $indexService->getProductList();
                $list->setVariantMode(\CoreShop\Model\Product\Listing::VARIANT_MODE_HIDE);

                $this->view->currentFilter = \CoreShop\Model\Product\Filter\Helper::setupProductList($list, $this->getAllParams(), $category->getFilterDefinition(), new \CoreShop\Model\Product\Filter\Service());

                $list->addCondition("parentCategoryIds LIKE '%,".$category->getId().",%'", "categoryIds");

                $this->view->filter = $category->getFilterDefinition();
                $this->view->list = $list;
                $this->view->params = $this->getAllParams();

                $paginator = Zend_Paginator::factory($list);
                $paginator->setCurrentPageNumber($this->getParam('page'));
                $paginator->setItemCountPerPage($list->getLimit());
                $paginator->setPageRange(10);

                $this->view->paginator = $paginator;
            } else {
                $this->view->paginator = $category->getProductsPaging($page, $perPage, $this->parseSorting($sort), true);
            }

            $this->view->category = $category;
            $this->view->page = $page;
            $this->view->sort = $sort;
            $this->view->perPage = $perPage;
            $this->view->type = $type;

            $this->view->seo = array(
                "image" => $category->getImage(),
                "description" => $category->getMetaDescription() ? $category->getMetaDescription() : $category->getDescription()
            );

            $this->view->headTitle($category->getMetaTitle() ? $category->getMetaTitle() : $category->getName());
        } else {
            throw new CoreShop\Exception(sprintf('Category with id "%s" not found', $id));
        }
    }

    protected function parseSorting($sortString)
    {
        $allowed = array("name", "price");
        $sort = array(
            "name" => "name",
            "direction" => "asc"
        );

        $sortString = explode("_", $sortString);

        if (count($sortString) < 2) {
            return $sort;
        }

        $name = strtolower($sortString[0]);
        $direction = strtolower($sortString[1]);

        if (in_array($name, $allowed) && in_array($direction, array("desc", "asc"))) {
            return array(
                "name" => $name,
                "direction" => $direction
            );
        }

        return $sort;
    }
}
