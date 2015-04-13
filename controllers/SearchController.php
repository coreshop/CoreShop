<?php

use CoreShop\Controller\Action;

use Pimcore\Model\Object\CoreShopProduct;

class CoreShop_SearchController extends Action
{
    public function searchAction()
    {
        $text = $this->view->searchText = $this->getParam("text");
        $page = $this->getParam("page", 1);
        $itemsPerPage = $this->getParam("perPage", 10);

        $query = array(
            "name LIKE ?",
            "description LIKE ?",
            "shortDescription LIKE ?",
            "metaTitle LIKE ?",
            "metaDescription LIKE ?"
        );
        $queryParams = array(
            '%' . $text . '%',
            '%' . $text . '%',
            '%' . $text . '%',
            '%' . $text . '%',
            '%' . $text . '%'
        );

        $list = new CoreShopProduct\Listing();
        $list->setCondition(implode(' OR ', $query), $queryParams);

        $paginator = \Zend_Paginator::factory($list);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($itemsPerPage);

        $this->view->paginator = $paginator;
    }
}