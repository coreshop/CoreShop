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
 * Class CoreShop_SearchController
 *
 * @todo: use index?
 */
class CoreShop_SearchController extends Action
{
    public function searchAction()
    {
        $text = $this->view->searchText = $this->getParam('text');
        $page = $this->getParam('page', 1);
        $itemsPerPage = $this->getParam('perPage', 10);

        $query = array(
            'name LIKE ?',
            'description LIKE ?',
            'shortDescription LIKE ?',
            'metaTitle LIKE ?',
            'metaDescription LIKE ?',
        );
        $queryParams = array(
            '%'.$text.'%',
            '%'.$text.'%',
            '%'.$text.'%',
            '%'.$text.'%',
            '%'.$text.'%',
        );

        $list = \CoreShop\Model\Product::getList();
        $list->setCondition(implode(' OR ', $query), $queryParams);

        $paginator = \Zend_Paginator::factory($list);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($itemsPerPage);

        $this->view->paginator = $paginator;
    }
}
