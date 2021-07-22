<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Report;

use Carbon\Carbon;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class CategoriesReport implements ReportInterface
{
    private int $totalRecords = 0;
    private RepositoryInterface $storeRepository;
    private Connection $db;
    private MoneyFormatterInterface $moneyFormatter;
    private LocaleContextInterface $localeService;
    private PimcoreRepositoryInterface $orderRepository;
    private PimcoreRepositoryInterface$categoryRepository;
    private PimcoreRepositoryInterface $orderItemRepository;

    public function __construct(
        RepositoryInterface $storeRepository,
        Connection $db,
        MoneyFormatterInterface $moneyFormatter,
        LocaleContextInterface $localeService,
        PimcoreRepositoryInterface $orderRepository,
        PimcoreRepositoryInterface $categoryRepository,
        PimcoreRepositoryInterface $orderItemRepository
    ) {
        $this->storeRepository = $storeRepository;
        $this->db = $db;
        $this->moneyFormatter = $moneyFormatter;
        $this->localeService = $localeService;
        $this->orderRepository = $orderRepository;
        $this->categoryRepository = $categoryRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    public function getReportData(ParameterBag $parameterBag): array
    {
        $fromFilter = $parameterBag->get('from', strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $storeId = $parameterBag->get('store', null);
        $orderCompleteState = OrderStates::STATE_COMPLETE;

        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $page = $parameterBag->get('page', 1);
        $limit = $parameterBag->get('limit', 25);
        $offset = $parameterBag->get('offset', $page === 1 ? 0 : ($page - 1) * $limit);

        $orderClassId = $this->orderRepository->getClassId();
        $categoryClassId = $this->categoryRepository->getClassId();
        $orderItemClassId = $this->orderItemRepository->getClassId();
        $locale = $this->localeService->getLocaleCode();

        if (null === $storeId) {
            return [];
        }

        $store = $this->storeRepository->find($storeId);
        if (!$store instanceof StoreInterface) {
            return [];
        }

        $query = "
            SELECT SQL_CALC_FOUND_ROWS
              `categories`.oo_id as categoryId,
              `categories`.o_key as categoryKey,
              `localizedCategories`.name as categoryName,
              `orders`.store,
              SUM(orderItems.totalGross) AS sales,
              SUM((orderItems.itemRetailPriceNet - orderItems.itemWholesalePrice) * orderItems.quantity) AS profit,
              SUM(orderItems.quantity) AS `quantityCount`,
              COUNT(orderItems.product__id) AS `orderCount`
            FROM object_$categoryClassId AS categories
            INNER JOIN object_localized_query_" . $categoryClassId . '_' . $locale . " AS localizedCategories ON localizedCategories.ooo_id = categories.oo_id 
            INNER JOIN dependencies AS catProductDependencies ON catProductDependencies.targetId = categories.oo_id AND catProductDependencies.targettype = \"object\" 
            INNER JOIN object_query_$orderItemClassId AS orderItems ON orderItems.product__id = catProductDependencies.sourceid
            INNER JOIN object_relations_$orderClassId AS orderRelations ON orderRelations.dest_id = orderItems.oo_id AND orderRelations.fieldname = \"items\"
            INNER JOIN object_query_$orderClassId AS `orders` ON `orders`.oo_id = orderRelations.src_id
            WHERE orders.store = $storeId AND orders.orderState = '$orderCompleteState' AND orders.orderDate > ? AND orders.orderDate < ? AND orderItems.product__id IS NOT NULL
            GROUP BY categories.oo_id
            ORDER BY quantityCount DESC
            LIMIT $offset,$limit";

        $results = $this->db->fetchAllAssociative($query, [$from->getTimestamp(), $to->getTimestamp()]);

        $this->totalRecords = (int) $this->db->fetchColumn('SELECT FOUND_ROWS()');

        $data = [];
        foreach ($results as $result) {
            $name = !empty($result['categoryName']) ? $result['categoryName'] : $result['categoryKey'];
            $data[] = [
                'name' => sprintf('%s (Id: %d)', $name, $result['categoryId']),
                'categoryName' => $name,
                'sales' => $result['sales'],
                'profit' => $result['profit'],
                'quantityCount' => $result['quantityCount'],
                'orderCount' => $result['orderCount'],
                'salesFormatted' => $this->moneyFormatter->format($result['sales'], $store->getCurrency()->getIsoCode(), $this->localeService->getLocaleCode()),
                'profitFormatted' => $this->moneyFormatter->format($result['profit'], $store->getCurrency()->getIsoCode(), $this->localeService->getLocaleCode()),
            ];
        }

        return array_values($data);
    }

    public function getTotal(): int
    {
        return $this->totalRecords;
    }
}
