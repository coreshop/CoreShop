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
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class ProductsReport implements ReportInterface
{
    /**
     * @var Connection
     */
    private $db;

    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @var array
     */
    private $pimcoreClasses;

    /**
     * @param Connection $db
     * @param MoneyFormatterInterface $moneyFormatter
     * @param array $pimcoreClasses
     */
    public function __construct(Connection $db, MoneyFormatterInterface $moneyFormatter, array $pimcoreClasses)
    {
        $this->db = $db;
        $this->moneyFormatter = $moneyFormatter;
        $this->pimcoreClasses = $pimcoreClasses;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(ParameterBag $parameterBag) {
        $fromFilter = $parameterBag->get('from' , strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $orderClassId = $this->pimcoreClasses['order'];
        $orderItemClassId = $this->pimcoreClasses['order_item'];
        $productClassId = $this->pimcoreClasses['product'];

        $query = "
            SELECT 
              orderItems.product__id,
              products.sku as name,
              SUM(orderItems.itemRetailPriceNet * orderItems.quantity) as sales, 
              AVG(orderItems.itemRetailPriceNet * orderItems.quantity) as salesPrice,
              SUM((orderItems.itemRetailPriceNet - orderItems.itemWholesalePrice) * orderItems.quantity) as profit,
              COUNT(orderItems.product__id) as count
            FROM object_query_$orderClassId AS orders
            INNER JOIN object_relations_$orderClassId as orderRelations ON orderRelations.src_id = orders.oo_id AND orderRelations.fieldname = \"items\"
            INNER JOIN object_query_$orderItemClassId AS orderItems ON orderRelations.dest_id = orderItems.oo_id
            INNER JOIN object_query_$productClassId AS products ON orderItems.product__id = products.oo_id
            WHERE orders.orderDate > ? AND orders.orderDate < ? AND orderItems.product__id IS NOT NULL
            GROUP BY orderItems.product__id
            ORDER BY COUNT(orderItems.product__id) DESC
        ";

        $productSales = $this->db->fetchAll($query, [$from->getTimestamp(), $to->getTimestamp()]);

        foreach ($productSales as &$sale) {
            $sale['salesPriceFormatted'] = $this->moneyFormatter->format($sale['salesPrice'], 'EUR');
            $sale['salesFormatted'] = $this->moneyFormatter->format($sale['sales'], 'EUR');
            $sale['profitFormatted'] = $this->moneyFormatter->format($sale['profit'], 'EUR');
        }

        return array_values($productSales);
    }
}
