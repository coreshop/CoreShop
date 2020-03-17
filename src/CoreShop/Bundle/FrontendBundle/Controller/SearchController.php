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

declare(strict_types=1);

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\FrontendBundle\Form\Type\SearchType;
use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfigurator;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zend\Paginator\Paginator;

class SearchController extends FrontendController
{
    public function widgetAction(
        FormFactoryInterface $formFactory,
        TemplateConfigurator $templateConfigurator
    ): Response {
        $form = $this->createSearchForm($formFactory);

        return $this->renderTemplate($templateConfigurator->findTemplate('Search/_widget.html'), [
            'form' => $form->createView(),
        ]);
    }

    public function searchAction(
        Request $request,
        FormFactoryInterface $formFactory,
        ShopperContextInterface $shopperContext,
        ProductRepositoryInterface $productRepository,
        TemplateConfigurator $templateConfigurator
    ): Response {
        $form = $this->createSearchForm($formFactory);
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
                '%'.$text.'%',
                '%'.$text.'%',
                '%'.$text.'%',
                '%'.$text.'%',
                '%'.$shopperContext->getStore()->getId().'%',
            ];

            $list = $productRepository->getList();
            $list->setCondition('active = 1 AND ('.implode(' OR ', $query).') AND stores LIKE ?', $queryParams);

            $paginator = new Paginator($list);
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($itemsPerPage);

            return $this->renderTemplate($templateConfigurator->findTemplate('Search/search.html'), [
                'paginator' => $paginator,
                'searchText' => $text,
            ]);
        }

        return $this->redirectToRoute('coreshop_index');
    }

    protected function createSearchForm(FormFactoryInterface $formFactory)
    {
        return $formFactory->createNamed(
            'search',
            SearchType::class,
            null, [
                'action' => $this->generateCoreShopUrl(null, 'coreshop_search'),
                'method' => 'GET',
            ]
        );
    }
}
