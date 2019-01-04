<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\FrontendBundle\Form\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Zend\Paginator\Paginator;

class SearchController extends FrontendController
{
    public function widgetAction(Request $request)
    {
        $form = $this->createSearchForm();

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Search/_widget.html'), [
            'form' => $form->createView(),
        ]);
    }

    public function searchAction(Request $request)
    {
        $form = $this->createSearchForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $formData = $form->getData();
            $text = $formData['text'];
            $page = $request->get('page', 1);
            $itemsPerPage = 10;

            $query = [
                'name LIKE ?',
                'description LIKE ?',
                'shortDescription LIKE ?',
                'sku LIKE ?',
            ];
            $queryParams = [
                '%' . $text . '%',
                '%' . $text . '%',
                '%' . $text . '%',
                '%' . $text . '%',
                '%' . $this->container->get('coreshop.context.store')->getStore()->getId() . '%',
            ];

            $list = $this->get('coreshop.repository.product')->getList();
            $list->setCondition('active = 1 AND (' . implode(' OR ', $query) . ') AND stores LIKE ?', $queryParams);

            $paginator = new Paginator($list);
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($itemsPerPage);

            return $this->renderTemplate($this->templateConfigurator->findTemplate('Search/search.html'), [
                'paginator' => $paginator,
                'searchText' => $text,
            ]);
        }

        return $this->redirectToRoute('coreshop_index');
    }

    protected function createSearchForm()
    {
        return $form = $this->get('form.factory')->createNamed('search', SearchType::class, null, [
            'action' => $this->generateCoreShopUrl(null, 'coreshop_search'),
            'method' => 'GET',
        ]);
    }
}
