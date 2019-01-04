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

namespace CoreShop\Bundle\CoreBundle\Report;

use Carbon\Carbon;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use CoreShop\Component\Product\Model\ManufacturerInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\DBAL\Connection;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\ParameterBag;

class ManufacturerReport implements ReportInterface
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
     * @var PimcoreRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var PimcoreRepositoryInterface
     */
    private $orderItemRepository;

    /**
     * @param RepositoryInterface        $storeRepository
     * @param Connection                 $db
     * @param MoneyFormatterInterface    $moneyFormatter
     * @param LocaleContextInterface     $localeService
     * @param PimcoreRepositoryInterface $orderRepository
     * @param PimcoreRepositoryInterface $orderItemRepository
     */
    public function __construct(
        RepositoryInterface $storeRepository,
        Connection $db,
        MoneyFormatterInterface $moneyFormatter,
        LocaleContextInterface $localeService,
        PimcoreRepositoryInterface $orderRepository,
        PimcoreRepositoryInterface $orderItemRepository
    ) {
        $this->storeRepository = $storeRepository;
        $this->db = $db;
        $this->moneyFormatter = $moneyFormatter;
        $this->localeService = $localeService;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
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

        $orderClassId = $this->orderRepository->getClassId();
        $orderItemClassId = $this->orderItemRepository->getClassId();
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

        $manufacturerSales = InheritanceHelper::useInheritedValues(function () use ($productSales) {
            $manufacturerSales = [];
            foreach ($productSales as $productSale) {
                $product = DataObject::getById($productSale['product__id']);
                if (!$product instanceof ProductInterface) {
                    continue;
                }

                $manufacturer = $product->getManufacturer();
                if (!$manufacturer instanceof ManufacturerInterface) {
                    continue;
                }

                $manufacturerId = $manufacturer->getId();
                if (!isset($manufacturerSales[$manufacturerId])) {
                    $name = !empty($manufacturer->getName()) ? $manufacturer->getName() : $manufacturer->getKey();
                    $manufacturerSales[$manufacturerId] = $productSale;
                    $manufacturerSales[$manufacturerId]['name'] = sprintf('%s (Id: %d)', $name, $manufacturer->getId());
                } else {
                    $manufacturerSales[$manufacturerId]['sales'] += $productSale['sales'];
                    $manufacturerSales[$manufacturerId]['profit'] += $productSale['profit'];
                    $manufacturerSales[$manufacturerId]['quantityCount'] += $productSale['quantityCount'];
                    $manufacturerSales[$manufacturerId]['orderCount'] += $productSale['orderCount'];
                }
            }

            return $manufacturerSales;
        });

        usort($manufacturerSales, function ($a, $b) {
            return $b['orderCount'] <=> $a['orderCount'];
        });

        foreach ($manufacturerSales as &$sale) {
            $sale['salesFormatted'] = $this->moneyFormatter->format($sale['sales'], $store->getCurrency()->getIsoCode(), $this->localeService->getLocaleCode());
            $sale['profitFormatted'] = $this->moneyFormatter->format($sale['profit'], $store->getCurrency()->getIsoCode(), $this->localeService->getLocaleCode());
        }

        return array_values($manufacturerSales);
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->totalRecords;
    }
}
