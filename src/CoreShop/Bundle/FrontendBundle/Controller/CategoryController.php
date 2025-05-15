<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

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
use Pimcore\Model\DataObject\Data\UrlSlug;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class CategoryController extends FrontendController
{
    protected string $repositoryIdentifier = 'oo_id';

    protected string $requestIdentifier = 'category';

    public function menuAction(): Response
    {
        $categories = $this->getRepository()->findFirstLevelForStore($this->getContext()->getStore());

        return $this->render($this->getTemplateConfigurator()->findTemplate('Category/_menu.html'), [
            'categories' => $categories,
        ]);
    }

    public function menuLeftAction(Request $request): Response
    {
        $activeCategory = $this->getParameterFromRequest($request, 'activeCategory');
        $activeSubCategories = [];

        $firstLevelCategories = $this->getRepository()->findFirstLevelForStore($this->getContext()->getStore());

        if ($activeCategory instanceof CategoryInterface) {
            $activeSubCategories = $this->getRepository()->findChildCategoriesForStore($activeCategory, $this->getContext()->getStore());
        }

        return $this->render($this->getTemplateConfigurator()->findTemplate('Category/_menu-left.html'), [
            'categories' => $firstLevelCategories,
            'activeCategory' => $activeCategory,
            'activeSubCategories' => $activeSubCategories,
        ]);
    }

    public function detailSlugAction(Request $request, CategoryInterface $object, UrlSlug $urlSlug): Response
    {
        return $this->detail($request, $object);
    }

    public function indexAction(Request $request): Response
    {
        $id = $this->getParameterFromRequest($request, $this->requestIdentifier);

        $category = $this->getRepository()->findOneBy([
            $this->repositoryIdentifier => $id,
            'pimcore_unpublished' => true,
        ]);

        if (!$category instanceof CategoryInterface) {
            throw new NotFoundHttpException(
                sprintf(
                    'category with identifier "%s" (%s) not found',
                    $this->repositoryIdentifier,
                    $this->getParameterFromRequest($request, $this->requestIdentifier),
                ),
            );
        }

        return $this->detail($request, $category);
    }

    public function detail(Request $request, CategoryInterface $category): Response
    {
        $this->denyAccessUnlessGranted('CORESHOP_CATEGORY_SHOW');

        $listModeDefault = $this->getConfigurationService()->getForStore('system.category.list.mode');
        $gridPerPageAllowed = $this->getConfigurationService()->getForStore('system.category.grid.per_page');
        $gridPerPageDefault = $this->getConfigurationService()->getForStore('system.category.grid.per_page.default');
        $listPerPageAllowed = $this->getConfigurationService()->getForStore('system.category.list.per_page');
        $listPerPageDefault = $this->getConfigurationService()->getForStore('system.category.list.per_page.default');
        $displaySubCategories = $this->getConfigurationService()->getForStore('system.category.list.include_subcategories');
        $variantMode = $this->getConfigurationService()->getForStore('system.category.variant_mode');

        $page = (int) $this->getParameterFromRequest($request, 'page', 1) ?: 1;
        $type = $this->getParameterFromRequest($request, 'type', $listModeDefault);

        $defaultPerPage = $type === 'list' ? $listPerPageDefault : $gridPerPageDefault;
        $allowedPerPage = $type === 'list' ? $listPerPageAllowed : $gridPerPageAllowed;

        $perPage = (int) $this->getParameterFromRequest($request, 'perPage', $defaultPerPage) ?: 10;

        $this->validateCategory($request, $category);

        $viewParameters = [];

        if ($category->getFilter() instanceof FilterInterface) {
            $filteredList = $this->container->get(FilteredListingFactoryInterface::class)->createList($category->getFilter(), $request->request);
            $filteredList->setLocale($request->getLocale());
            $filteredList->setVariantMode($variantMode ? $variantMode : ListingInterface::VARIANT_MODE_HIDE);
            $filteredList->addCondition(new LikeCondition('stores', 'both', sprintf('%1$s%2$s%1$s', ',', $this->getContext()->getStore()->getId())), 'stores');
            $filteredList->addCondition(new LikeCondition('parentCategoryIds', 'both', sprintf(',%s,', $category->getId())), 'parentCategoryIds');

            $orderDirection = $category->getFilter()->getOrderDirection();
            $orderKey = $category->getFilter()->getOrderKey();

            $sortKey = (empty($orderKey) ? $this->getParameter('coreshop.frontend.category.default_sort_name') : $orderKey) . '_' . (empty($orderDirection) ? $this->getParameter('coreshop.frontend.category.default_sort_direction') : $orderDirection);
            $sort = $this->getParameterFromRequest($request, 'sort', $sortKey);
            $sortParsed = $this->parseSorting($sort);

            $filteredList->setOrderKey($sortParsed['name']);
            $filteredList->setOrder($sortParsed['direction']);

            $currentFilter = $this->container->get(FilterProcessorInterface::class)->processConditions($category->getFilter(), $filteredList, $request->query);
            $preparedConditions = $this->container->get(FilterProcessorInterface::class)->prepareConditionsForRendering($category->getFilter(), $filteredList, $currentFilter);

            $paginator = $this->getPaginator()->paginate(
                $filteredList,
                $page,
                $perPage,
            );

            $viewParameters['list'] = $filteredList;
            $viewParameters['filter'] = $category->getFilter();
            $viewParameters['currentFilter'] = $currentFilter;
            $viewParameters['paginator'] = $paginator;
            $viewParameters['conditions'] = $preparedConditions;
        } else {
            //Classic Listing Mode
            $sort = $this->getParameterFromRequest($request, 'sort', $this->getParameter('coreshop.frontend.category.default_sort_name') . '_' . $this->getParameter('coreshop.frontend.category.default_sort_direction'));
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
                $perPage,
            );

            $viewParameters['paginator'] = $paginator;
        }

        $viewParameters['category'] = $category;
        $viewParameters['page'] = $page;
        $viewParameters['perPage'] = $perPage;
        $viewParameters['type'] = $type;
        $viewParameters['perPageAllowed'] = $allowedPerPage;
        $viewParameters['sort'] = $sort;
        $viewParameters['validSortElements'] = $this->getParameter('coreshop.frontend.category.valid_sort_options');

        foreach ($paginator as $product) {
            $this->container->get(TrackerInterface::class)->trackProductImpression($product);
        }

        $this->container->get(SEOPresentationInterface::class)->updateSeoMetadata($category);

        return $this->render($this->getTemplateConfigurator()->findTemplate('Category/index.html'), $viewParameters);
    }

    protected function validateCategory(Request $request, CategoryInterface $category): void
    {
        $isFrontendRequestByAdmin = false;

        if ($this->container->get(RequestHelper::class)->isFrontendRequestByAdmin($request)) {
            $isFrontendRequestByAdmin = true;
        }

        if ($isFrontendRequestByAdmin === false && !$category->isPublished()) {
            throw new NotFoundHttpException('category not found');
        }

        if (!in_array($this->getContext()->getStore()->getId(), array_values($category->getStores()))) {
            throw new NotFoundHttpException(sprintf(sprintf('store (id %s) not available in category', $this->getContext()->getStore()->getId())));
        }
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

        if (in_array($name, $this->getParameter('coreshop.frontend.category.valid_sort_options')) && in_array($direction, ['desc', 'asc'])) {
            return [
                'name' => $name,
                'direction' => $direction,
            ];
        }

        return $sort;
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            new SubscribedService('validSortProperties', 'array', attributes: new Autowire('%coreshop.frontend.category.valid_sort_options%')),
            new SubscribedService('defaultSortName', 'string', attributes: new Autowire('%coreshop.frontend.category.default_sort_name%')),
            new SubscribedService('defaultSortDirection', 'string', attributes: new Autowire('%coreshop.frontend.category.default_sort_direction%')),
            'coreshop.repository.category' => CategoryRepositoryInterface::class,
            ConfigurationServiceInterface::class,
            PaginatorInterface::class,
            TrackerInterface::class,
            FilteredListingFactoryInterface::class,
            FilterProcessorInterface::class,
            new SubscribedService('coreshop.repository.product', ProductRepositoryInterface::class),
        ]);
    }

    protected function getRepository(): CategoryRepositoryInterface
    {
        return $this->container->get('coreshop.repository.category');
    }

    protected function getProductRepository(): ProductRepositoryInterface
    {
        return $this->container->get('coreshop.repository.product');
    }

    protected function getConfigurationService(): ConfigurationServiceInterface
    {
        return $this->container->get(ConfigurationServiceInterface::class);
    }

    protected function getContext(): ShopperContextInterface
    {
        return $this->container->get(ShopperContextInterface::class);
    }

    protected function getPaginator(): PaginatorInterface
    {
        return $this->container->get(PaginatorInterface::class);
    }
}
