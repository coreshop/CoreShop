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

class VouchersReport implements ReportInterface
{
    /**
     * @var int
     */
    private $totalRecords = 0;

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
     * @param Connection              $db
     * @param MoneyFormatterInterface $moneyFormatter
     * @param array                   $pimcoreClasses
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
    public function getData(ParameterBag $parameterBag)
    {
        $fromFilter = $parameterBag->get('from', strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $page = $parameterBag->get('page', 1);
        $limit = $parameterBag->get('limit', 25);
        $offset = $parameterBag->get('offset', $page === 1 ? 0 : ($page-1)*$limit);

        $classId = $this->pimcoreClasses['order'];

        $data = [];

        $sqlQuery = "
              SELECT SQL_CALC_FOUND_ROWS
              orderVouchers.voucherCode AS code, 
              priceRule.name AS rule,
              orderVouchers.discountGross AS discount,
              orders.orderDate
              FROM object_collection_CoreShopProposalCartPriceRuleItem_$classId as orderVouchers
              INNER JOIN object_query_$classId as orders ON orders.oo_id = orderVouchers.o_id 
              INNER JOIN element_workflow_state AS orderState ON orders.oo_id = orderState.cid 
              LEFT JOIN coreshop_cart_price_rule AS priceRule ON orderVouchers.cartPriceRule = priceRule.id 
              WHERE orderState.ctype = 'object' AND orderState.state = 'complete' AND orders.orderDate > ? AND orders.orderDate < ?
              ORDER BY orders.orderDate DESC
              LIMIT $offset,$limit";

        $results = $this->db->fetchAll($sqlQuery, [$from->getTimestamp(), $to->getTimestamp()]);
        $this->totalRecords = (int)$this->db->fetchOne('SELECT FOUND_ROWS()');

        foreach ($results as $result) {
            $date = Carbon::createFromTimestamp($result['orderDate']);
            $data[] = [
                'usedDate' => $date->getTimestamp(),
                'code'     => $result['code'],
                'rule'     => !empty($result['rule']) ? $result['rule'] : '--',
                'discount' => $this->moneyFormatter->format($result['discount'], 'EUR')
            ];
        }

        return array_values($data);
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->totalRecords;
    }
}
