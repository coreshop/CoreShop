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
use CoreShop\Component\Core\Report\ExportReportInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class VouchersReport implements ReportInterface, ExportReportInterface
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
    private $localeContext;

    /**
     * @var PimcoreRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param RepositoryInterface $storeRepository
     * @param Connection $db
     * @param MoneyFormatterInterface $moneyFormatter
     * @param LocaleContextInterface $localeContext
     * @param PimcoreRepositoryInterface $orderRepository
     */
    public function __construct(
        RepositoryInterface $storeRepository,
        Connection $db,
        MoneyFormatterInterface $moneyFormatter,
        LocaleContextInterface $localeContext,
        PimcoreRepositoryInterface $orderRepository
    )
    {
        $this->storeRepository = $storeRepository;
        $this->db = $db;
        $this->moneyFormatter = $moneyFormatter;
        $this->localeContext = $localeContext;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getReportData(ParameterBag $parameterBag)
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

        $classId = $this->orderRepository->getClassId();

        if (is_null($storeId)) {
            return [];
        }

        $store = $this->storeRepository->find($storeId);
        if (!$store instanceof StoreInterface) {
            return [];
        }

        $data = [];

        $sqlQuery = "
              SELECT SQL_CALC_FOUND_ROWS
              orderVouchers.voucherCode AS code, 
              priceRule.name AS rule,
              orderVouchers.discountGross AS discount,
              orders.orderDate
              FROM object_collection_CoreShopProposalCartPriceRuleItem_$classId as orderVouchers
              INNER JOIN object_query_$classId as orders ON orders.oo_id = orderVouchers.o_id 
              LEFT JOIN coreshop_cart_price_rule AS priceRule ON orderVouchers.cartPriceRule = priceRule.id 
              WHERE orderVouchers.voucherCode <> '' AND orders.store = $storeId AND orders.orderState = '$orderCompleteState' AND orders.orderDate > ? AND orders.orderDate < ?
              ORDER BY orders.orderDate DESC
              LIMIT $offset,$limit";

        $results = $this->db->fetchAll($sqlQuery, [$from->getTimestamp(), $to->getTimestamp()]);
        $this->totalRecords = (int) $this->db->fetchColumn('SELECT FOUND_ROWS()');

        foreach ($results as $result) {
            $date = Carbon::createFromTimestamp($result['orderDate']);
            $data[] = [
                'usedDate' => $date->getTimestamp(),
                'code' => $result['code'],
                'rule' => !empty($result['rule']) ? $result['rule'] : '--',
                'discount' => $this->moneyFormatter->format($result['discount'], $store->getCurrency()->getIsoCode(), $this->localeContext->getLocaleCode())
            ];
        }

        return array_values($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getExportReportData(ParameterBag $parameterBag)
    {
        $data = $this->getReportData($parameterBag);

        $formatter = new \IntlDateFormatter($this->localeContext->getLocaleCode(), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);

        foreach ($data as &$entry)
        {
            $entry['usedDate'] = $formatter->format($entry['usedDate']);
        }

        return $data;
    }


    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->totalRecords;
    }
}
