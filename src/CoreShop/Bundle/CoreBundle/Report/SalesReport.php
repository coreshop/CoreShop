<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Report;

use Carbon\Carbon;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Portlet\ExportPortletInterface;
use CoreShop\Component\Core\Portlet\PortletInterface;
use CoreShop\Component\Core\Report\ExportReportInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class SalesReport implements ReportInterface, ExportReportInterface, PortletInterface, ExportPortletInterface
{
    private int $totalRecords = 0;

    public function __construct(
        private RepositoryInterface $storeRepository,
        private Connection $db,
        private MoneyFormatterInterface $moneyFormatter,
        private LocaleContextInterface $localeContext,
        private PimcoreRepositoryInterface $orderRepository,
    ) {
    }

    public function getReportData(ParameterBag $parameterBag): array
    {
        return $this->getData($parameterBag);
    }

    public function getPortletData(ParameterBag $parameterBag): array
    {
        return $this->getData($parameterBag);
    }

    protected function getData(ParameterBag $parameterBag): array
    {
        $groupBy = $parameterBag->get('groupBy', 'day');
        $fromFilter = $parameterBag->get('from', strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $storeId = $parameterBag->get('store', null);
        $orderCompleteState = OrderStates::STATE_COMPLETE;

        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $classId = $this->orderRepository->getClassId();

        $data = [];

        $dateFormatter = null;
        $groupSelector = '';

        if (null === $storeId) {
            return [];
        }

        $store = $this->storeRepository->find($storeId);
        if (!$store instanceof StoreInterface) {
            return [];
        }

        switch ($groupBy) {
            case 'day':
                $dateFormatter = 'd-m-Y';
                $groupSelector = 'DATE(FROM_UNIXTIME(orders.orderDate))';

                break;
            case 'month':
                $dateFormatter = 'F Y';
                $groupSelector = 'MONTH(FROM_UNIXTIME(orders.orderDate))';

                break;
            case 'year':
                $dateFormatter = 'Y';
                $groupSelector = 'YEAR(FROM_UNIXTIME(orders.orderDate))';

                break;
        }

        $sqlQuery = "
              SELECT DATE(FROM_UNIXTIME(orderDate)) AS dayDate, orderDate, SUM(totalGross) AS total 
              FROM object_query_$classId as orders
              WHERE orders.store = $storeId AND orders.orderState = '$orderCompleteState' AND orders.orderDate > ? AND orders.orderDate < ? AND saleState='" . OrderSaleStates::STATE_ORDER . "' 
              GROUP BY " . $groupSelector;

        $results = $this->db->fetchAllAssociative($sqlQuery, [$from->getTimestamp(), $to->getTimestamp()]);

        foreach ($results as $result) {
            $date = Carbon::createFromTimestamp($result['orderDate']);

            $data[] = [
                'timestamp' => $date->getTimestamp(),
                'datetext' => $date->format($dateFormatter),
                'sales' => $result['total'],
                'salesFormatted' => $this->moneyFormatter->format((int) $result['total'], $store->getCurrency()->getIsoCode(), $this->localeContext->getLocaleCode()),
            ];
        }

        return $data;
    }

    public function getExportReportData(ParameterBag $parameterBag): array
    {
        $data = $this->getReportData($parameterBag);

        $formatter = new \IntlDateFormatter($this->localeContext->getLocaleCode(), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);

        foreach ($data as &$entry) {
            $entry['timestamp'] = $formatter->format($entry['timestamp']);

            unset($entry['datetext'], $entry['sales']);
        }

        return $data;
    }

    public function getExportPortletData(ParameterBag $parameterBag): array
    {
        return $this->getExportReportData($parameterBag);
    }

    /**
     * {@inheritd}
     */
    public function getTotal(): int
    {
        return $this->totalRecords;
    }
}
