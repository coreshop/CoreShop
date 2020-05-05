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

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Index\Condition\LikeCondition;
use CoreShop\Component\Index\Factory\FilteredListingFactoryInterface;
use CoreShop\Component\Index\Filter\FilterProcessorInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Pimcore\Routing\LinkGeneratorInterface;
use CoreShop\Component\Resource\Model\AbstractObject;
use CoreShop\Component\SEO\SEOPresentationInterface;
use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Pimcore\Http\RequestHelper;
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
        $isFrontendRequestByAdmin = false;
        $category = $this->getRepository()->findOneBy([$this->repositoryIdentifier => $request->get($this->requestIdentifier), 'pimcore_unpublished' => true]);

        if (!$category instanceof CategoryInterface) {
            throw new NotFoundHttpException(sprintf(sprintf('category with identifier "%s" (%s) not found', $this->repositoryIdentifier, $request->get($this->requestIdentifier))));
        }

        if ($this->get(RequestHelper::class)->isFrontendRequestByAdmin($request)) {
            $isFrontendRequestByAdmin = true;
        }

        if ($isFrontendRequestByAdmin === false && !$category->isPublished()) {
            throw new NotFoundHttpException('category not found');
        }

        if (!in_array($this->getContext()->getStore()->getId(), array_values($category->getStores()))) {
            throw new NotFoundHttpException(sprintf(sprintf('store (id %s) not available in category', $this->getContext()->getStore()->getId())));
        }

        $urlToBe = $this->get(LinkGeneratorInterface::class)->generate($category);

        if (urldecode($request->getBaseUrl().$request->getPathInfo()) !== $urlToBe) {
            return $this->redirect($urlToBe);
        }

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = $defaultPerPage;
        }

        $paginator = null;

        $viewParameters = [];

        if ($category->getFilter() instanceof FilterInterface) {
            $filteredList = $this->get(FilteredListingFactoryInterface::class)->createList($category->getFilter(), $request->request);
            $filteredList->setLocale($request->getLocale());
            $filteredList->setVariantMode($variantMode ? $variantMode : ListingInterface::VARIANT_MODE_HIDE);
            $filteredList->addCondition(new LikeCondition('stores', 'both', sprintf('%1$s%2$s%1$s', ',', $this->getContext()->getStore()->getId())), 'stores');
            $filteredList->setCategory($category);

            $orderDirection = $category->getFilter()->getOrderDirection();
            $orderKey = $category->getFilter()->getOrderKey();

            $sortKey = (empty($orderKey) ? $this->defaultSortName : strtoupper($orderKey)) . '_' . (empty($orderDirection) ? $this->defaultSortDirection : strtoupper($orderDirection));
            $sort = $request->get('sort', $sortKey);
            $sortParsed = $this->parseSorting($sort);

            $filteredList->setOrderKey($sortParsed['name']);
            $filteredList->setOrder($sortParsed['direction']);

            $currentFilter = $this->get(FilterProcessorInterface::class)->processConditions($category->getFilter(), $filteredList, $request->query);
            $preparedConditions = $this->get(FilterProcessorInterface::class)->prepareConditionsForRendering($category->getFilter(), $filteredList, $currentFilter);

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

            $list = $this->getProductRepository()->getProductsListing($options);

            $paginator = new Paginator($list);
            $paginator->setItemCountPerPage($perPage);
            $paginator->setCurrentPageNumber($page);

            $viewParameters['paginator'] = $paginator;
        }

        $viewParameters['category'] = $category;
        $viewParameters['page'] = $page;
        $viewParameters['perPage'] = $perPage;
        $viewParameters['type'] = $type;
        $viewParameters['perPageAllowed'] = $allowedPerPage;
        $viewParameters['sort'] = $sort;
        $viewParameters['validSortElements'] = $this->validSortProperties;

        foreach ($paginator as $product) {
            $this->get(TrackerInterface::class)->trackProductImpression($product);
        }

        $this->get(SEOPresentationInterface::class)->updateSeoMetadata($category);

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
    protected function getConfigurationService(): ConfigurationServiceInterface
    {
        return $this->get(ConfigurationServiceInterface::class);
    }

    /**
     * @return ShopperContextInterface
     */
    protected function getContext(): ShopperContextInterface
    {
        return $this->get(ShopperContextInterface::class);
    }
}
