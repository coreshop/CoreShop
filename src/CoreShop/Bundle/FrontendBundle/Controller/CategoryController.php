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
use CoreShop\Component\Resource\Model\AbstractObject;
use CoreShop\Component\SEO\SEOPresentationInterface;
use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Pimcore\Http\RequestHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends FrontendController
{
    protected array $validSortProperties = ['name'];
    protected string $repositoryIdentifier = 'oo_id';
    protected string $requestIdentifier = 'category';
    protected string $defaultSortName = 'name';
    protected string $defaultSortDirection = 'asc';

    public function menuAction(): Response
    {
        $categories = $this->getRepository()->findForStore($this->getContext()->getStore());

        return $this->render($this->templateConfigurator->findTemplate('Category/_menu.html'), [
            'categories' => $categories,
        ]);
    }

    public function menuLeftAction(Request $request): Response
    {
        $activeCategory = $request->get('activeCategory');
        $activeSubCategories = [];

        $firstLevelCategories = $this->getRepository()->findFirstLevelForStore($this->getContext()->getStore());

        if ($activeCategory instanceof CategoryInterface) {
            $activeSubCategories = $this->getRepository()->findChildCategoriesForStore($activeCategory, $this->getContext()->getStore());
        }

        return $this->render($this->templateConfigurator->findTemplate('Category/_menu-left.html'), [
            'categories' => $firstLevelCategories,
            'activeCategory' => $activeCategory,
            'activeSubCategories' => $activeSubCategories,
        ]);
    }

    public function indexAction(Request $request): Response
    {
        $listModeDefault = $this->getConfigurationService()->getForStore('system.category.list.mode');
        $gridPerPageAllowed = $this->getConfigurationService()->getForStore('system.category.grid.per_page');
        $gridPerPageDefault = $this->getConfigurationService()->getForStore('system.category.grid.per_page.default');
        $listPerPageAllowed = $this->getConfigurationService()->getForStore('system.category.list.per_page');
        $listPerPageDefault = $this->getConfigurationService()->getForStore('system.category.list.per_page.default');
        $displaySubCategories = $this->getConfigurationService()->getForStore('system.category.list.include_subcategories');
        $variantMode = $this->getConfigurationService()->getForStore('system.category.variant_mode');

        $page = $request->get('page', 1);
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

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = $defaultPerPage;
        }

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

            $paginator = $this->getPaginator()->paginate(
                $filteredList,
                $page,
                $perPage
            );

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

            $paginator = $this->getPaginator()->paginate(
                $list,
                $page,
                $perPage
            );

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

        return $this->render($this->templateConfigurator->findTemplate('Category/index.html'), $viewParameters);
    }

    protected function parseSorting(string $sortString): array
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

    /**
     * @return PaginatorInterface
     */
    protected function getPaginator(): PaginatorInterface
    {
        return $this->get(PaginatorInterface::class);
    }
}
