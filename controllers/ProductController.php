<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

use CoreShopTemplate\Controller\Action;

use Pimcore\Model\Object\CoreShopProduct;
use Pimcore\Model\Object\CoreShopCategory;

class CoreShop_ProductController extends Action {
    
    public function detailAction () {
        $id = $this->getParam("product");
        $product = CoreShopProduct::getById($id);
        
        if($product instanceof CoreShopProduct)
        {
            $this->view->product = $product;
            
            $this->view->seo = array(
                "image" => $product->getImage(),
                "description" => $product->getMetaDescription() ? $product->getMetaDescription() : $product->getShortDescription()
            );
            
            $this->view->headTitle($product->getMetaTitle() ? $product->getMetaTitle() : $product->getName());
        }
        else
        {
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
        $product = CoreShopProduct::getById($id);

        $this->disableLayout();

        if($product instanceof $product)
        {
            $this->view->product = $product;
        }
        else
        {
            throw new \Exception(sprintf("Product with id %s not found", $id));
        }
    }
    
    public function listAction() {
        $id = $this->getParam("category");
        $page = $this->getParam("page", 0);
        $sort = $this->getParam("sort", "NAMEA");
        $perPage = $this->getParam("perPage", 12);
        $type = $this->getParam("type", "list");

        $category = CoreShopCategory::getById($id);

        if($category instanceof CoreShopCategory) {
            $this->view->category = $category;
            $this->view->paginator = $category->getProductsPaging($page, $perPage, $this->parseSorting($sort), true);

            $this->view->page = $page;
            $this->view->sort = $sort;
            $this->view->perPage = $perPage;
            $this->view->type = $type;

            $this->view->seo = array(
                "image" => $category->getImage(),
                "description" => $category->getMetaDescription() ? $category->getMetaDescription() : $category->getDescription()
            );

            $this->view->headTitle($category->getMetaTitle() ? $category->getMetaTitle() : $category->getName());
        }
        else {
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

        if(count($sortString) < 2)
            return $sort;

        $name = strtolower($sortString[0]);
        $direction = strtolower($sortString[1]);

        if(in_array($name, $allowed) && in_array($direction, array("desc", "asc")))
        {
            return array(
                "name" => $name,
                "direction" => $direction
            );
        }

        return $sort;
    }
}
