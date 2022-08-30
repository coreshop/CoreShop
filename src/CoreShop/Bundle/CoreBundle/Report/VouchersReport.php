<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

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
    private int $totalRecords = 0;

    public function __construct(private RepositoryInterface $storeRepository, private Connection $db, private MoneyFormatterInterface $moneyFormatter, private LocaleContextInterface $localeContext, private PimcoreRepositoryInterface $orderRepository)
    {
    }

    public function getReportData(ParameterBag $parameterBag): array
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

        if (null === $storeId) {
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

        $results = $this->db->fetchAllAssociative($sqlQuery, [$from->getTimestamp(), $to->getTimestamp()]);
        $this->totalRecords = (int)$this->db->fetchOne('SELECT FOUND_ROWS()');

        foreach ($results as $result) {
            $date = Carbon::createFromTimestamp($result['orderDate']);
            $data[] = [
                'usedDate' => $date->getTimestamp(),
                'code' => $result['code'],
                'rule' => !empty($result['rule']) ? $result['rule'] : '--',
                'discount' => $this->moneyFormatter->format((int)$result['discount'], $store->getCurrency()->getIsoCode(), $this->localeContext->getLocaleCode()),
            ];
        }

        return $data;
    }

    public function getExportReportData(ParameterBag $parameterBag): array
    {
        $data = $this->getReportData($parameterBag);

        $formatter = new \IntlDateFormatter($this->localeContext->getLocaleCode(), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);

        foreach ($data as &$entry) {
            $entry['usedDate'] = $formatter->format($entry['usedDate']);
        }

        return $data;
    }

    public function getTotal(): int
    {
        return $this->totalRecords;
    }
}
