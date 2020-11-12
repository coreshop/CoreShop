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

namespace CoreShop\Bundle\CoreBundle\Report;

use Carbon\Carbon;
use CoreShop\Bundle\ResourceBundle\Pimcore\Repository\StackRepository;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Report\ExportReportInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class ProductsReport implements ReportInterface, ExportReportInterface
{
    private $totalRecords = 0;
    private $storeRepository;
    private $db;
    private $localeContext;
    private $moneyFormatter;
    private $productStackRepository;
    private $orderRepository;
    private $orderItemRepository;

    public function __construct(
        RepositoryInterface $storeRepository,
        Connection $db,
        MoneyFormatterInterface $moneyFormatter,
        LocaleContextInterface $localeContext,
        PimcoreRepositoryInterface $orderRepository,
        PimcoreRepositoryInterface $orderItemRepository,
        StackRepository $productStackRepository
    ) {
        $this->storeRepository = $storeRepository;
        $this->db = $db;
        $this->moneyFormatter = $moneyFormatter;
        $this->localeContext = $localeContext;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productStackRepository = $productStackRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getReportData(ParameterBag $parameterBag): array
    {
        $fromFilter = $parameterBag->get('from', strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $objectTypeFilter = $parameterBag->get('objectType', 'all');
        $storeId = (int)$parameterBag->get('store', null);

        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $page = $parameterBag->get('page', 1);
        $limit = $parameterBag->get('limit', 50);
        $offset = $parameterBag->get('offset', $page === 1 ? 0 : ($page - 1) * $limit);

        $orderClassId = $this->orderRepository->getClassId();
        $orderItemClassId = $this->orderItemRepository->getClassId();
        $orderCompleteState = OrderStates::STATE_COMPLETE;

        $locale = $this->localeContext->getLocaleCode();

        if (null === $storeId) {
            return [];
        }

        $store = $this->storeRepository->find($storeId);
        if (!$store instanceof StoreInterface) {
            return [];
        }

        if ($objectTypeFilter === 'container') {
            $unionData = [];
            foreach ($this->productStackRepository->getClassIds() as $id) {
                $unionData[] = 'SELECT `o_id`, `name`, `o_type` FROM object_localized_' . $id . '_' . $locale;
            }

            $union = join(' UNION ALL ', $unionData);

            $query = "
              SELECT SQL_CALC_FOUND_ROWS
                products.o_id as productId,
                products.`name` as productName,
                SUM(orderItems.totalGross) AS sales, 
                AVG(orderItems.totalGross) AS salesPrice,
                SUM((orderItems.itemRetailPriceNet - orderItems.itemWholesalePrice) * orderItems.quantity) AS profit,
                SUM(orderItems.quantity) AS `quantityCount`,
                COUNT(`order`.oo_id) AS `orderCount`
                FROM ($union) AS products
                INNER JOIN object_query_$orderItemClassId AS orderItems ON products.o_id = orderItems.mainObjectId
                INNER JOIN object_relations_$orderClassId AS orderRelations ON orderRelations.dest_id = orderItems.oo_id AND orderRelations.fieldname = \"items\"
                INNER JOIN object_query_$orderClassId AS `order` ON `order`.oo_id = orderRelations.src_id
                WHERE products.o_type = 'object' AND `order`.store = $storeId AND `order`.orderState = '$orderCompleteState' AND `order`.orderDate > ? AND `order`.orderDate < ?
                GROUP BY products.o_id
            LIMIT $offset,$limit";
        } else {
            $productTypeCondition = '1=1';
            if ($objectTypeFilter === 'object') {
                $productTypeCondition = 'orderItems.mainObjectId = NULL';
            } elseif ($objectTypeFilter === 'variant') {
                $productTypeCondition = 'orderItems.mainObjectId IS NOT NULL';
            }

            $query = "
                SELECT SQL_CALC_FOUND_ROWS
                  orderItems.objectId as productId,
                  orderItemsTranslated.name AS `productName`,
                  
                  SUM(orderItems.totalGross) AS sales, 
                  AVG(orderItems.totalGross) AS salesPrice,
                  SUM((orderItems.itemRetailPriceNet - orderItems.itemWholesalePrice) * orderItems.quantity) AS profit,
                  
                  SUM(orderItems.quantity) AS `quantityCount`,
                  COUNT(orderItems.objectId) AS `orderCount`
                FROM object_query_$orderClassId AS orders
                INNER JOIN object_relations_$orderClassId AS orderRelations ON orderRelations.src_id = orders.oo_id AND orderRelations.fieldname = \"items\"
                INNER JOIN object_query_$orderItemClassId AS orderItems ON orderRelations.dest_id = orderItems.oo_id
                INNER JOIN object_localized_query_" . $orderItemClassId . '_' . $locale . " AS orderItemsTranslated ON orderItems.oo_id = orderItemsTranslated.ooo_id
                WHERE `orders`.store = $storeId AND $productTypeCondition AND `orders`.orderState = '$orderCompleteState' AND `orders`.orderDate > ? AND `orders`.orderDate < ?
                GROUP BY orderItems.objectId
                ORDER BY orderCount DESC
                LIMIT $offset,$limit";
        }

        $productSales = $this->db->fetchAllAssociative($query, [$from->getTimestamp(), $to->getTimestamp()]);

        $this->totalRecords = (int) $this->db->fetchColumn('SELECT FOUND_ROWS()');

        foreach ($productSales as &$sale) {
            $sale['salesPriceFormatted'] = $this->moneyFormatter->format($sale['salesPrice'], $store->getCurrency()->getIsoCode(), $locale);
            $sale['salesFormatted'] = $this->moneyFormatter->format($sale['sales'], $store->getCurrency()->getIsoCode(), $locale);
            $sale['profitFormatted'] = $this->moneyFormatter->format($sale['profit'], $store->getCurrency()->getIsoCode(), $locale);
            $sale['name'] = $sale['productName'] . ' (Id: ' . $sale['productId'] . ')';
        }

        return array_values($productSales);
    }

    public function getExportReportData(ParameterBag $parameterBag): array
    {
        $data = $this->getReportData($parameterBag);

        foreach ($data as &$entry) {
            unset($entry['salesPrice']);
            unset($entry['sales']);
            unset($entry['profit']);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal(): int
    {
        return $this->totalRecords;
    }
}
