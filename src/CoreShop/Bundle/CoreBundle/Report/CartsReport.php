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

namespace CoreShop\Bundle\CoreBundle\Report;

use Carbon\Carbon;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Portlet\PortletInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class CartsReport implements ReportInterface, PortletInterface
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
     * @var PimcoreRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var PimcoreRepositoryInterface
     */
    private $cartRepository;

    /**
     * @param RepositoryInterface        $storeRepository
     * @param Connection                 $db
     * @param PimcoreRepositoryInterface $orderRepository,
     * @param PimcoreRepositoryInterface $cartRepository
     */
    public function __construct(
        RepositoryInterface $storeRepository,
        Connection $db,
        PimcoreRepositoryInterface $orderRepository,
        PimcoreRepositoryInterface $cartRepository
    ) {
        $this->storeRepository = $storeRepository;
        $this->db = $db;
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getReportData(ParameterBag $parameterBag)
    {
        return $this->getData($parameterBag);
    }

    /**
     * {@inheritdoc}
     */
    public function getPortletData(ParameterBag $parameterBag)
    {
        return $this->getData($parameterBag);
    }

    /**
     * @param ParameterBag $parameterBag
     *
     * @return array
     */
    protected function getData(ParameterBag $parameterBag)
    {
        $fromFilter = $parameterBag->get('from', strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $storeId = $parameterBag->get('store', null);

        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $fromTimestamp = $from->getTimestamp();
        $toTimestamp = $to->getTimestamp();

        $orderClassId = $this->orderRepository->getClassId();
        $cartClassId = $this->cartRepository->getClassId();

        if (is_null($storeId)) {
            return [];
        }

        $store = $this->storeRepository->find($storeId);

        if (!$store instanceof StoreInterface) {
            return [];
        }

        $queries = [];
        foreach (['LEFT', 'RIGHT'] as $join) {
            $queries[] = "
                SELECT
                    CASE WHEN orderDateTimestamp IS NULL THEN cartDateTimestamp ELSE orderDateTimestamp END as timestamp,
                    CASE WHEN orderCount IS NULL THEN 0 ELSE orderCount END as orders,
                    CASE WHEN cartCount IS NULL THEN 0 ELSE cartCount END as carts
                FROM (
                  SELECT 
                    COUNT(*) as orderCount,
                    DATE(FROM_UNIXTIME(orderDate)) as orderDateTimestamp
                  FROM object_query_$orderClassId AS orders
                  WHERE store = $storeId AND orderDate > $fromTimestamp AND orderDate < $toTimestamp
                  GROUP BY DATE(FROM_UNIXTIME(orderDate))
                ) as ordersQuery
                $join OUTER JOIN (
                  SELECT
                    COUNT(*) as cartCount,
                    DATE(FROM_UNIXTIME(o_creationDate)) as cartDateTimestamp
                  FROM object_$cartClassId AS carts
                  WHERE store = $storeId AND o_creationDate > $fromTimestamp AND o_creationDate < $toTimestamp
                  GROUP BY DATE(FROM_UNIXTIME(o_creationDate))
                ) as cartsQuery ON cartsQuery.cartDateTimestamp = ordersQuery.orderDateTimestamp
            ";
        }

        $data = $this->db->fetchAll(implode(PHP_EOL . 'UNION ALL' . PHP_EOL, $queries) . '  ORDER BY timestamp ASC');

        foreach ($data as &$day) {
            $date = Carbon::createFromTimestamp(strtotime($day['timestamp']));

            $day['datetext'] = $date->toDateString();
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
