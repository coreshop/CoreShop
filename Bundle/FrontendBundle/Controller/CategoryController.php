<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreFrontendController;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Product\Model\CategoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Zend\Paginator\Paginator;

class CategoryController extends PimcoreFrontendController
{
    public function menuAction(Request $request)
    {
        $categoryList = $this->repository->getListingClass();
        $categoryList->setLimit(5);

        return $this->render('CoreShopFrontendBundle:Category:_menu.html.twig', [
            'categories' => $categoryList->getObjects()
        ]);
    }

    public function menuLeftAction(Request $request) {
        $categoryList = $this->repository->getListingClass();
        $categoryList->setCondition("parentCategory__id is null AND stores LIKE '%,".$this->getStoreContext()->getStore()->getId().",%'");

        return $this->render('CoreShopFrontendBundle:Category:_menu-left.html.twig', [
            'categories' => $categoryList->getObjects()
        ]);
    }

    public function indexAction(Request $request, $name, $categoryId) {
        //TODO: add some of the old configurations
        
        $page = $request->get('page', 0);
        $sort = $request->get('sort', 'NAMEA');
        $sortParsed = $this->parseSorting($sort);
        $type = $request->get('type', 'list');
        $perPage = $request->get('perPage', 20);
        
        $category = $this->repository->find($categoryId);

        if (!$category instanceof CategoryInterface) {
            return $this->redirectToRoute('coreshop_shop_index');
        }

        //if (!in_array($this->shopperContext->getStore()->getId())) TODO: Check for allowed Stores
        $paginator = null;

        $viewParameters = [

        ];

        if ($category->getFilter() instanceof FilterInterface) {
            $filteredList = $this->get('coreshop.factory.filter.list')->createList($category->getFilter(), $request->request);
            $filteredList->setVariantMode(ListingInterface::VARIANT_MODE_HIDE);
            $filteredList->setCategory($category);

            $currentFilter = $this->get('coreshop.filter.processor')->processConditions($category->getFilter(), $filteredList, $request->query);
            $preparedConditions = $this->get('coreshop.filter.processor')->prepareConditionsForRendering($category->getFilter(), $filteredList, $currentFilter);

            $paginator = new Paginator($filteredList);
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($perPage);
            $paginator->setPageRange(10);

            $viewParameters['list'] = $filteredList;
            $viewParameters['filter'] = $category->getFilter();
            $viewParameters['currentFilter'] = $currentFilter;
            $viewParameters['paginator'] = $paginator;
            $viewParameters['conditions'] = $preparedConditions;
        }
        else {
            //Classic Listing Mode
            $list = $this->get('coreshop.repository.product')->getListingClass();

            $condition = "enabled = 1";
            $condition .= " AND categories LIKE '%,".$category->getId().",%'";
            $condition .= " AND stores LIKE '%,".$this->shopperContext->getStore()->getId().",%'";

            $list->setCondition($condition);
            $list->setOrderKey($sortParsed['name']);
            $list->setOrder($sortParsed['direction']);

            $paginator = new Paginator($list);
            $paginator->setItemCountPerPage($perPage);
            $paginator->setCurrentPageNumber($page);

            $viewParameters['paginator'] = $paginator;
        }

        $viewParameters['category'] = $category;
        $viewParameters['page'] = $page;
        $viewParameters['perPage'] = $perPage;
        $viewParameters['type'] = $type;
        $viewParameters['perPageAllowed'] = [10, 20, 30, 40, 50];
        $viewParameters['sort'] = $sort;

        return $this->render('CoreShopFrontendBundle:Category:index.html.twig', $viewParameters);
    }

    /**
     * @param $sortString
     * @return array
     */
    protected function parseSorting($sortString)
    {
        $allowed = ['name', 'price'];
        $sort = [
            'name' => 'name',
            'direction' => 'asc',
        ];

        $sortString = explode('_', $sortString);

        if (count($sortString) < 2) {
            return $sort;
        }

        $name = strtolower($sortString[0]);
        $direction = strtolower($sortString[1]);

        if (in_array($name, $allowed) && in_array($direction, ['desc', 'asc'])) {
            return [
                'name' => $name,
                'direction' => $direction,
            ];
        }

        return $sort;
    }

    /**
     * @return StoreContextInterface
     */
    public function getStoreContext() {
        return $this->get('coreshop.context.store');
    }
}
