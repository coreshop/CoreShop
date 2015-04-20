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