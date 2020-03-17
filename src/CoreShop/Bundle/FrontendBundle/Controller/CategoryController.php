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

use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
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
use CoreShop\Component\Resource\Model\AbstractObject;
use CoreShop\Component\SEO\SEOPresentationInterface;
use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    public function menuAction(
        CategoryRepositoryInterface $categoryRepository,
        ShopperContextInterface $shopperContext,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $categories = $categoryRepository->findForStore($shopperContext->getStore());

        return $this->renderTemplate($templateConfigurator->findTemplate('Category/_menu.html'), [
            'categories' => $categories,
        ]);
    }

    public function menuLeftAction(
        Request $request,
        CategoryRepositoryInterface $categoryRepository,
        ShopperContextInterface $shopperContext,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $activeCategory = $request->get('activeCategory');
        $activeSubCategories = [];

        $firstLevelCategories = $categoryRepository->findFirstLevelForStore($shopperContext->getStore());

        if ($activeCategory instanceof CategoryInterface) {
            $activeSubCategories = $categoryRepository->findChildCategoriesForStore(
                $activeCategory,
                $shopperContext->getStore()
            );
        }

        return $this->renderTemplate($templateConfigurator->findTemplate('Category/_menu-left.html'), [
            'categories' => $firstLevelCategories,
            'activeCategory' => $activeCategory,
            'activeSubCategories' => $activeSubCategories,
        ]);
    }

    public function indexAction(
        Request $request,
        ConfigurationServiceInterface $configurationService,
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository,
        ShopperContextInterface $shopperContext,
        FilteredListingFactoryInterface $filteredListingFactory,
        FilterProcessorInterface $filterProcessor,
        TrackerInterface $tracker,
        SEOPresentationInterface $seo,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $listModeDefault = $configurationService->getForStore('system.category.list.mode');
        $gridPerPageAllowed = $configurationService->getForStore('system.category.grid.per_page');
        $gridPerPageDefault = $configurationService->getForStore('system.category.grid.per_page.default');
        $listPerPageAllowed = $configurationService->getForStore('system.category.list.per_page');
        $listPerPageDefault = $configurationService->getForStore('system.category.list.per_page.default');
        $displaySubCategories = $configurationService->getForStore('system.category.list.include_subcategories');
        $variantMode = $configurationService->getForStore('system.category.variant_mode');

        $page = $request->get('page', 0);
        $type = $request->get('type', $listModeDefault);

        $defaultPerPage = $type === 'list' ? $listPerPageDefault : $gridPerPageDefault;
        $allowedPerPage = $type === 'list' ? $listPerPageAllowed : $gridPerPageAllowed;

        $perPage = $request->get('perPage', $defaultPerPage);

        $category = $categoryRepository->findOneBy([$this->repositoryIdentifier => $request->get($this->requestIdentifier)]);

        if (!$category instanceof CategoryInterface) {
            throw new NotFoundHttpException(sprintf(sprintf('category with identifier "%s" (%s) not found',
                $this->repositoryIdentifier, $request->get($this->requestIdentifier))));
        }

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = $defaultPerPage;
        }

        if (!in_array($shopperContext->getStore()->getId(), array_values($category->getStores()))) {
            throw new NotFoundHttpException(
                sprintf('store (id %s) not available in category',
                    $shopperContext->getStore()->getId())
            );
        }

        $paginator = null;

        $viewParameters = [];

        if ($category->getFilter() instanceof FilterInterface) {
            $filteredList = $filteredListingFactory->createList($category->getFilter(), $request->request);
            $filteredList->setLocale($request->getLocale());
            $filteredList->setVariantMode($variantMode ?? ListingInterface::VARIANT_MODE_HIDE);
            $filteredList->addCondition(
                new LikeCondition(
                    'stores',
                    'both',
                    sprintf('%1$s%2$s%1$s', ',', $shopperContext->getStore()->getId())
                ),
                'stores'
            );
            $filteredList->setCategory($category);

            $orderDirection = $category->getFilter()->getOrderDirection();
            $orderKey = $category->getFilter()->getOrderKey();

            $sortKey = (empty($orderKey) ? $this->defaultSortName : strtoupper($orderKey)).'_'.(empty($orderDirection) ? $this->defaultSortDirection : strtoupper($orderDirection));
            $sort = $request->get('sort', $sortKey);
            $sortParsed = $this->parseSorting($sort);

            $filteredList->setOrderKey($sortParsed['name']);
            $filteredList->setOrder($sortParsed['direction']);

            $currentFilter = $filterProcessor->processConditions(
                $category->getFilter(),
                $filteredList,
                $request->query
            );
            $preparedConditions = $filterProcessor->prepareConditionsForRendering(
                $category->getFilter(),
                $filteredList,
                $currentFilter
            );

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
            $sort = $request->get('sort', $this->defaultSortName.'_'.$this->defaultSortDirection);
            $sortParsed = $this->parseSorting($sort);

            $categories = [$category];
            if ($displaySubCategories === true) {
                foreach ($categoryRepository->findRecursiveChildCategoriesForStore($category,
                    $shopperContext->getStore()) as $subCategory) {
                    $categories[] = $subCategory;
                }
            }

            $options = [
                'order_key' => $sortParsed['name'],
                'order' => $sortParsed['direction'],
                'categories' => $categories,
                'store' => $shopperContext->getStore(),
                'return_type' => 'list',
            ];

            if ($variantMode !== ListingInterface::VARIANT_MODE_HIDE) {
                $options['object_types'] = [AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT];
            }

            $list = $productRepository->getProductsListing($options);

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
            $tracker->trackProductImpression($product);
        }

        $seo->updateSeoMetadata($category);

        return $this->renderTemplate($templateConfigurator->findTemplate('Category/index.html'), $viewParameters);
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
}
