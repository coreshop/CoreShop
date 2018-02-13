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
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Pimcore\InheritanceHelper;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\DBAL\Connection;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\ParameterBag;

class CategoriesReport implements ReportInterface
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
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @var LocaleContextInterface
     */
    private $localeService;

    /**
     * @var array
     */
    private $pimcoreClasses;

    /**
     * CategoriesReport constructor.
     *
     * @param RepositoryInterface $storeRepository
     * @param Connection $db
     * @param MoneyFormatterInterface $moneyFormatter
     * @param LocaleContextInterface $localeService
     * @param array $pimcoreClasses
     */
    public function __construct(
        RepositoryInterface $storeRepository,
        Connection $db,
        MoneyFormatterInterface $moneyFormatter,
        LocaleContextInterface $localeService,
        array $pimcoreClasses
    )
    {
        $this->storeRepository = $storeRepository;
        $this->db = $db;
        $this->moneyFormatter = $moneyFormatter;
        $this->localeService = $localeService;
        $this->pimcoreClasses = $pimcoreClasses;
    }

    /**
     * {@inheritdoc}
     */
    public function getReportData(ParameterBag $parameterBag)
    {
        $fromFilter = $parameterBag->get('from', strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $storeId = $parameterBag->get('store', null);
        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $orderClassId = $this->pimcoreClasses['order'];
        $orderItemClassId = $this->pimcoreClasses['order_item'];
        $orderCompleteState = OrderStates::STATE_COMPLETE;

        if (is_null($storeId)) {
            return [];
        }

        $store = $this->storeRepository->find($storeId);
        if (!$store instanceof StoreInterface) {
            return [];
        }


        $query = "
            SELECT 
              orderItems.product__id,
              SUM(orderItems.totalGross) AS sales, 
              SUM((orderItems.itemRetailPriceNet - orderItems.itemWholesalePrice) * orderItems.quantity) AS profit,
              SUM(orderItems.quantity) AS `quantityCount`,
              COUNT(orderItems.product__id) AS `orderCount`
            FROM object_query_$orderClassId AS orders
            INNER JOIN object_relations_$orderClassId AS orderRelations ON orderRelations.src_id = orders.oo_id AND orderRelations.fieldname = \"items\"
            INNER JOIN object_query_$orderItemClassId AS orderItems ON orderRelations.dest_id = orderItems.oo_id
            WHERE orders.store = $storeId AND orders.orderState = '$orderCompleteState' AND orders.orderDate > ? AND orders.orderDate < ? AND orderItems.product__id IS NOT NULL
            GROUP BY orderItems.product__id
            ORDER BY COUNT(orderItems.product__id) DESC
        ";

        $productSales = $this->db->fetchAll($query, [$from->getTimestamp(), $to->getTimestamp()]);

        $catSales = InheritanceHelper::useInheritedValues(function () use ($productSales) {

            $catSales = [];
            foreach ($productSales as $productSale) {
                $product = DataObject::getById($productSale['product__id']);
                if ($product instanceof ProductInterface) {
                    $categories = $product->getCategories();
                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            $catId = $category->getId();
                            if (!isset($catSales[$catId])) {
                                $catSales[$catId] = $productSale;
                                $catSales[$catId]['name'] = $category->getName();
                            } else {
                                $catSales[$catId]['sales'] += $productSale['sales'];
                                $catSales[$catId]['profit'] += $productSale['profit'];
                                $catSales[$catId]['quantityCount'] += $productSale['quantityCount'];
                                $catSales[$catId]['orderCount'] += $productSale['orderCount'];
                            }
                        }
                    }
                }
            }

            return $catSales;

        });

        usort($catSales, function ($a, $b) {
            return $b['orderCount'] <=> $a['orderCount'];
        });

        foreach ($catSales as &$sale) {
            $sale['salesFormatted'] = $this->moneyFormatter->format($sale['sales'], $store->getCurrency()->getIsoCode(), $this->localeService->getLocaleCode());
            $sale['profitFormatted'] = $this->moneyFormatter->format($sale['profit'], $store->getCurrency()->getIsoCode(), $this->localeService->getLocaleCode());
        }

        return array_values($catSales);
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->totalRecords;
    }
}
