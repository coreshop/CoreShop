<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
use CoreShop\Component\Index\Condition\Condition;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Resource\Model\AbstractObject;
use CoreShop\Component\Store\Model\StoreInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zend\Paginator\Paginator;

class CategoryController extends FrontendController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function menuAction(Request $request)
    {
        $categories = $this->getRepository()->findForStore($this->getContext()->getStore());

        return $this->renderTemplate('CoreShopFrontendBundle:Category:_menu.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function menuLeftAction(Request $request)
    {
        $activeCategory = $request->get('activeCategory');
        $activeSubCategories = [];

        $firstLevelCategories = $this->getRepository()->findFirstLevelForStore($this->getContext()->getStore());

        if ($activeCategory instanceof CategoryInterface) {
            $activeSubCategories = $this->getRepository()->findChildCategoriesForStore($activeCategory, $this->getContext()->getStore());
        }

        return $this->renderTemplate('CoreShopFrontendBundle:Category:_menu-left.html.twig', [
            'categories' => $firstLevelCategories,
            'activeCategory' => $activeCategory,
            'activeSubCategories' => $activeSubCategories
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $listModeDefault = $this->getConfigurationService()->getForStore('system.category.list.mode');
        $gridPerPageAllowed = $this->getConfigurationService()->getForStore("system.category.grid.per_page");
        $gridPerPageDefault = $this->getConfigurationService()->getForStore("system.category.grid.per_page_default");
        $listPerPageAllowed = $this->getConfigurationService()->getForStore("system.category.list.per_page");
        $listPerPageDefault = $this->getConfigurationService()->getForStore("system.category.list.per_page_default");
        $variantMode = $this->getConfigurationService()->getForStore("system.category.variant_mode");

        $page = $request->get('page', 0);
        $sort = $request->get('sort', 'NAMEA');
        $sortParsed = $this->parseSorting($sort);
        $type = $request->get('type', $listModeDefault);

        $defaultPerPage = $type === "list" ? $listPerPageDefault : $gridPerPageDefault;
        $allowedPerPage = $type === "list" ? $listPerPageAllowed : $gridPerPageAllowed;

        $perPage = $request->get('perPage', $defaultPerPage);

        $category = $this->getRepository()->find($request->get("category"));

        if (!$category instanceof CategoryInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = $defaultPerPage;
        }

        if (!in_array($this->getContext()->getStore()->getId(), array_values($category->getStores()))) {
            return $this->redirectToRoute('coreshop_index');
        }

        $paginator = null;

        $viewParameters = [];

        if ($category->getFilter() instanceof FilterInterface) {
            $filteredList = $this->get('coreshop.factory.filter.list')->createList($category->getFilter(), $request->request);
            $filteredList->setVariantMode($variantMode ? $variantMode : ListingInterface::VARIANT_MODE_HIDE);
            $filteredList->addCondition(Condition::like('stores', $this->getContext()->getStore()->getId(), 'both'), 'stores');
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
        } else {
            //Classic Listing Mode
            $list = $this->get('coreshop.repository.product')->getList();

            $condition = 'active = 1';
            $condition .= " AND categories LIKE '%,object|" . $category->getId() . ",%'";
            $condition .= " AND stores LIKE '%," . $this->getContext()->getStore()->getId() . ",%'";

            $list->setCondition($condition);
            $list->setOrderKey($sortParsed['name']);
            $list->setOrder($sortParsed['direction']);

            if ($variantMode !== ListingInterface::VARIANT_MODE_HIDE) {
                $list->setObjectTypes([AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT]);
            }

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

        foreach ($paginator as $product) {
            $this->get('coreshop.tracking.manager')->trackPurchasableImpression($product);
        }

        return $this->renderTemplate('CoreShopFrontendBundle:Category:index.html.twig', $viewParameters);
    }

    /**
     * @param $sortString
     *
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
     * @return CategoryRepositoryInterface
     */
    protected function getRepository()
    {
        return $this->get('coreshop.repository.category');
    }

    /**
     * @return \CoreShop\Component\Core\Configuration\ConfigurationService
     */
    protected function getConfigurationService()
    {
        return $this->get('coreshop.configuration.service');
    }

    /**
     * @return ShopperContextInterface
     */
    protected function getContext()
    {
        return $this->get('coreshop.context.shopper');
    }
}
