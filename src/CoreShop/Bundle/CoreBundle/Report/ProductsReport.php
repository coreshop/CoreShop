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

namespace CoreShop\Bundle\CoreBundle\Report;

use Carbon\Carbon;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class ProductsReport implements ReportInterface
{
    /**
     * @var int
     */
    private $totalRecords = 0;

    /**
     * @var RepositoryInterface
     */
    private $storeRepository;

    /**
     * @var Connection
     */
    private $db;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @var array
     */
    private $pimcoreClasses;

    /**
     * @var array
     */
    private $productStackIds;

    /**
     * @param RepositoryInterface $storeRepository
     * @param Connection $db
     * @param MoneyFormatterInterface $moneyFormatter
     * @param LocaleContextInterface $localeContext
     * @param array $pimcoreClasses
     * @param array $productStackIds
     */
    public function __construct(
        RepositoryInterface $storeRepository,
        Connection $db,
        MoneyFormatterInterface $moneyFormatter,
        LocaleContextInterface $localeContext,
        array $pimcoreClasses,
        array $productStackIds
    )
    {
        $this->storeRepository = $storeRepository;
        $this->db = $db;
        $this->moneyFormatter = $moneyFormatter;
        $this->localeContext = $localeContext;
        $this->pimcoreClasses = $pimcoreClasses;
        $this->productStackIds = $productStackIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getReportData(ParameterBag $parameterBag)
    {
        $fromFilter = $parameterBag->get('from', strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $objectTypeFilter = $parameterBag->get('objectType', 'all');
        $storeId = $parameterBag->get('store', null);

        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $page = $parameterBag->get('page', 1);
        $limit = $parameterBag->get('limit', 50);
        $offset = $parameterBag->get('offset', $page === 1 ? 0 : ($page - 1) * $limit);

        $orderClassId = $this->pimcoreClasses['order'];
        $orderItemClassId = $this->pimcoreClasses['order_item'];
        $orderCompleteState = OrderStates::STATE_COMPLETE;

        $locale = $this->localeContext->getLocaleCode();

        if (is_null($storeId)) {
            return [];
        }

        $store = $this->storeRepository->find($storeId);
        if (!$store instanceof StoreInterface) {
            return [];
        }

        if ($objectTypeFilter === 'container') {
            $unionData = [];
            foreach ($this->productStackIds as $id) {
                $unionData[] = 'SELECT `o_id`, `name`, `o_type` FROM object_localized_'.$id.'_'.$locale;
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
                INNER JOIN object_localized_query_".$orderItemClassId."_".$locale." AS orderItemsTranslated ON orderItems.oo_id = orderItemsTranslated.ooo_id
                WHERE `orders`.store = $storeId AND $productTypeCondition AND `orders`.orderState = '$orderCompleteState' AND `orders`.orderDate > ? AND `orders`.orderDate < ?
                GROUP BY orderItems.objectId
                ORDER BY orderCount DESC
                LIMIT $offset,$limit";
        }

        $productSales = $this->db->fetchAll($query, [$from->getTimestamp(), $to->getTimestamp()]);

        $this->totalRecords = (int) $this->db->fetchOne('SELECT FOUND_ROWS()');

        foreach ($productSales as &$sale) {
            $sale['salesPriceFormatted'] = $this->moneyFormatter->format($sale['salesPrice'], $store->getCurrency()->getIsoCode(), $locale);
            $sale['salesFormatted'] = $this->moneyFormatter->format($sale['sales'], $store->getCurrency()->getIsoCode(), $locale);
            $sale['profitFormatted'] = $this->moneyFormatter->format($sale['profit'], $store->getCurrency()->getIsoCode(), $locale);
            $sale['name'] = $sale['productName'].' (Id: '.$sale['productId'].')';
        }

        return array_values($productSales);
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->totalRecords;
    }
}
