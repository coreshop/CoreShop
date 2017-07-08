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
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class CategoriesReport implements ReportInterface
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
     * @var string
     */
    private $orderClassId;

    /**
     * @var string
     */
    private $orderItemClassId;

    /**
     * @var string
     */
    private $productClassId;

    /**
     * @var string
     */
    private $categoryClassId;

    /**
     * @var LocaleContextInterface
     */
    private $localeService;

    /**
     * @param Connection $db
     * @param MoneyFormatterInterface $moneyFormatter
     * @param string $orderClassId
     * @param string $orderItemClassId
     * @param string $productClassId
     * @param $categoryClassId
     * @param LocaleContextInterface $localeService
     */
    public function __construct(Connection $db, MoneyFormatterInterface $moneyFormatter, $orderClassId, $orderItemClassId, $productClassId, $categoryClassId, LocaleContextInterface $localeService)
    {
        $this->db = $db;
        $this->moneyFormatter = $moneyFormatter;
        $this->orderClassId = $orderClassId;
        $this->orderItemClassId = $orderItemClassId;
        $this->productClassId = $productClassId;
        $this->categoryClassId = $categoryClassId;
        $this->localeService = $localeService;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(ParameterBag $parameterBag) {
        $fromFilter = $parameterBag->get('from' , strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $orderClassId = $this->orderClassId;
        $orderItemClassId = $this->orderItemClassId;
        $productClassId = $this->productClassId;
        $categoryClassId = $this->categoryClassId;
        $categoryLocalizedQuery = $categoryClassId . "_" . $this->localeService->getLocaleCode();

        $query = "
            SELECT 
              category.oo_id as id,
              categoryLocalized.name,
              SUM(orderItems.itemRetailPriceNet * orderItems.quantity) as sales, 
              SUM((orderItems.itemRetailPriceNet - orderItems.itemWholesalePrice) * orderItems.quantity) as profit,
              COUNT(category.oo_id) as count
            FROM object_query_$orderClassId AS orders
            INNER JOIN object_relations_$orderClassId as orderRelations ON orderRelations.src_id = orders.oo_id AND orderRelations.fieldname = \"items\"
            INNER JOIN object_query_$orderItemClassId AS orderItems ON orderRelations.dest_id = orderItems.oo_id
            INNER JOIN object_query_$productClassId AS products ON orderItems.product__id = products.oo_id
            INNER JOIN object_relations_$productClassId as productRelations ON productRelations.src_id = products.oo_id AND productRelations.fieldname = \"categories\"
            INNER JOIN object_query_$categoryClassId as category ON productRelations.dest_id = category.oo_id
            INNER JOIN object_localized_query_$categoryLocalizedQuery as categoryLocalized ON categoryLocalized.ooo_id = category.oo_id
            WHERE orders.orderDate > ? AND orders.orderDate < ? AND orderItems.product__id IS NOT NULL
            GROUP BY category.oo_id
            ORDER BY COUNT(category.oo_id) DESC
        ";

        $catSales = $this->db->fetchAll($query, [$from->getTimestamp(), $to->getTimestamp()]);

        foreach ($catSales as &$sale) {
            $sale['salesFormatted'] = $this->moneyFormatter->format($sale['sales'], 'EUR');
            $sale['profitFormatted'] = $this->moneyFormatter->format($sale['profit'], 'EUR');
        }

        return array_values($catSales);
    }
}
