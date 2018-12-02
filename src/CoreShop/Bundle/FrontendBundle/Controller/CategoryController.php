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

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Index\Condition\LikeCondition;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Resource\Model\AbstractObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zend\Paginator\Paginator;

class CategoryController extends FrontendController
{
    /**
     * @var array
     */
    protected $validSortProperties = ['name'];

    /**
     * @var string
     */
    protected $repositoryIdentifier = 'oo_id';

    /**
     * @var string
     */
    protected $requestIdentifier = 'category';

    /**
     * @var string
     */
    protected $defaultSortName = 'name';

    /**
     * @var string
     */
    protected $defaultSortDirection = 'asc';

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function menuAction(Request $request)
    {
        $categories = $this->getRepository()->findForStore($this->getContext()->getStore());

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Category/_menu.html'), [
            'categories' => $categories,
        ]);
    }

    /**
     * @param Request $request
     *
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

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Category/_menu-left.html'), [
            'categories' => $firstLevelCategories,
            'activeCategory' => $activeCategory,
            'activeSubCategories' => $activeSubCategories,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $listModeDefault = $this->getConfigurationService()->getForStore('system.category.list.mode');
        $gridPerPageAllowed = $this->getConfigurationService()->getForStore('system.category.grid.per_page');
        $gridPerPageDefault = $this->getConfigurationService()->getForStore('system.category.grid.per_page.default');
        $listPerPageAllowed = $this->getConfigurationService()->getForStore('system.category.list.per_page');
        $listPerPageDefault = $this->getConfigurationService()->getForStore('system.category.list.per_page.default');
        $displaySubCategories = $this->getConfigurationService()->getForStore('system.category.list.include_subcategories');
        $variantMode = $this->getConfigurationService()->getForStore('system.category.variant_mode');

        $page = $request->get('page', 0);
        $type = $request->get('type', $listModeDefault);

        $defaultPerPage = $type === 'list' ? $listPerPageDefault : $gridPerPageDefault;
        $allowedPerPage = $type === 'list' ? $listPerPageAllowed : $gridPerPageAllowed;

        $perPage = $request->get('perPage', $defaultPerPage);

        $category = $this->getRepository()->findOneBy([$this->repositoryIdentifier => $request->get($this->requestIdentifier)]);
        if (!$category instanceof CategoryInterface) {
            throw new NotFoundHttpException(sprintf(sprintf('category with identifier "%s" (%s) not found', $this->repositoryIdentifier, $request->get($this->requestIdentifier))));
        }

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = $defaultPerPage;
        }

        if (!in_array($this->getContext()->getStore()->getId(), array_values($category->getStores()))) {
            throw new NotFoundHttpException(sprintf(sprintf('store (id %s) not available in category', $this->getContext()->getStore()->getId())));
        }

        $paginator = null;

        $viewParameters = [];

        if ($category->getFilter() instanceof FilterInterface) {
            $filteredList = $this->get('coreshop.factory.filter.list')->createList($category->getFilter(), $request->request);
            $filteredList->setLocale($request->getLocale());
            $filteredList->setVariantMode($variantMode ? $variantMode : ListingInterface::VARIANT_MODE_HIDE);
            $filteredList->addCondition(new LikeCondition('stores', 'both', $this->getContext()->getStore()->getId()), 'stores');
            $filteredList->setCategory($category);

            $orderDirection = $category->getFilter()->getOrderDirection();
            $orderKey = $category->getFilter()->getOrderKey();

            $sortKey = (empty($orderKey) ? $this->defaultSortName : strtoupper($orderKey)) . '_' . (empty($orderDirection) ? $this->defaultSortDirection : strtoupper($orderDirection));
            $sort = $request->get('sort', $sortKey);
            $sortParsed = $this->parseSorting($sort);

            $filteredList->setOrderKey($sortParsed['name']);
            $filteredList->setOrder($sortParsed['direction']);

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
            $sort = $request->get('sort', $this->defaultSortName . '_' . $this->defaultSortDirection);
            $sortParsed = $this->parseSorting($sort);

            $categories = [$category];
            if ($displaySubCategories === true) {
                foreach ($this->getRepository()->findRecursiveChildCategoriesForStore($category, $this->getContext()->getStore()) as $subCategory) {
                    $categories[] = $subCategory;
                }
            }

            $options = [
                'order_key' => $sortParsed['name'],
                'order' => $sortParsed['direction'],
                'categories' => $categories,
                'store' => $this->getContext()->getStore(),
                'return_type' => 'list',
            ];

            if ($variantMode !== ListingInterface::VARIANT_MODE_HIDE) {
                $options['object_types'] = [AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT];
            }

            $list = $this->getProductRepository()->getProducts($options);

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
        $viewParameters['validSortElements'] = $this->validSortProperties;

        foreach ($paginator as $product) {
            $this->get('coreshop.tracking.manager')->trackProductImpression($product);
        }

        $this->get('coreshop.seo.presentation')->updateSeoMetadata($category);

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Category/index.html'), $viewParameters);
    }

    /**
     * @param string $sortString
     *
     * @return array
     */
    protected function parseSorting($sortString)
    {
        $sort = [
            'name' => 'name',
            'direction' => 'asc',
        ];

        $sortString = explode('_', $sortString);

        if (count($sortString) < 2) {
            return $sort;
        }

        $name = $sortString[0];
        $direction = $sortString[1];

        if (in_array($name, $this->validSortProperties) && in_array($direction, ['desc', 'asc'])) {
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
     * @return ProductRepositoryInterface
     */
    protected function getProductRepository()
    {
        return $this->get('coreshop.repository.product');
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
