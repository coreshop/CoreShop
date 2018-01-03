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

namespace CoreShop\Bundle\CoreBundle\Portlet;

use Carbon\Carbon;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Portlet\PortletInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Pimcore\InheritanceHelper;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\DBAL\Connection;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\ParameterBag;

class OrderCartPortlet implements PortletInterface
{
    /**
     * @var Connection
     */
    private $db;

    /**
     * @var RepositoryInterface
     */
    private $storeRepository;

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
     * @param Connection              $db
     * @param RepositoryInterface     $storeRepository
     * @param MoneyFormatterInterface $moneyFormatter
     * @param LocaleContextInterface  $localeService
     * @param array                   $pimcoreClasses
     */
    public function __construct(
        Connection $db,
        RepositoryInterface $storeRepository,
        MoneyFormatterInterface $moneyFormatter,
        LocaleContextInterface $localeService,
        array $pimcoreClasses
    ) {
        $this->db = $db;
        $this->storeRepository = $storeRepository;
        $this->moneyFormatter = $moneyFormatter;
        $this->localeService = $localeService;
        $this->pimcoreClasses = $pimcoreClasses;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(ParameterBag $parameterBag)
    {
        $fromFilter = $parameterBag->get('from', strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $storeId = $parameterBag->get('store', 1);

        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $fromTimestamp = $from->getTimestamp();
        $toTimestamp = $to->getTimestamp();

        $orderClassId = $this->pimcoreClasses['order'];
        $cartClassId = $this->pimcoreClasses['cart'];

        $queries = [];

        $store = $this->storeRepository->find($storeId);
        if (!$store instanceof StoreInterface) {
            return [];
        }

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
                  INNER JOIN element_workflow_state AS orderState ON orders.oo_id = orderState.cid 
                  WHERE orders.store = $storeId AND orderState.ctype = 'object' AND orderState.state = 'complete' AND orderDate > $fromTimestamp AND orderDate < $toTimestamp
                  GROUP BY DATE(FROM_UNIXTIME(orderDate))
                ) as ordersQuery
                $join OUTER JOIN (
                  SELECT
                    COUNT(*) as cartCount,
                    DATE(FROM_UNIXTIME(o_creationDate)) as cartDateTimestamp
                  FROM object_$cartClassId AS carts
                  WHERE carts.store = $storeId AND o_creationDate > $fromTimestamp AND o_creationDate < $toTimestamp
                  GROUP BY DATE(FROM_UNIXTIME(o_creationDate))
                ) as cartsQuery ON cartsQuery.cartDateTimestamp = ordersQuery.orderDateTimestamp";
        }

        $data = $this->db->fetchAll(implode(PHP_EOL . 'UNION ALL' . PHP_EOL, $queries) . '  ORDER BY timestamp ASC');

        foreach ($data as &$day) {
            $date = Carbon::createFromTimestamp(strtotime($day['timestamp']));
            $day['datetext'] = $date->toDateString();
        }

        return $data;
    }
}
