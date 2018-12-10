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
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Portlet\ExportPortletInterface;
use CoreShop\Component\Core\Portlet\PortletInterface;
use CoreShop\Component\Core\Report\ExportReportInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class SalesReport implements ReportInterface, ExportReportInterface, PortletInterface, ExportPortletInterface
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
     * @param RepositoryInterface        $storeRepository
     * @param Connection                 $db
     * @param MoneyFormatterInterface    $moneyFormatter
     * @param LocaleContextInterface     $localeContext
     * @param PimcoreRepositoryInterface $orderRepository,
     */
    public function __construct(
        RepositoryInterface $storeRepository,
        Connection $db,
        MoneyFormatterInterface $moneyFormatter,
        LocaleContextInterface $localeContext,
        PimcoreRepositoryInterface $orderRepository
    ) {
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

        if (is_null($storeId)) {
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
              WHERE orders.store = $storeId AND orders.orderState = '$orderCompleteState' AND orders.orderDate > ? AND orders.orderDate < ? 
              GROUP BY " . $groupSelector;

        $results = $this->db->fetchAll($sqlQuery, [$from->getTimestamp(), $to->getTimestamp()]);

        foreach ($results as $result) {
            $date = Carbon::createFromTimestamp($result['orderDate']);

            $data[] = [
                'timestamp' => $date->getTimestamp(),
                'datetext' => $date->format($dateFormatter),
                'sales' => $result['total'],
                'salesFormatted' => $this->moneyFormatter->format($result['total'], $store->getCurrency()->getIsoCode(), $this->localeContext->getLocaleCode()),
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

        foreach ($data as &$entry) {
            $entry['timestamp'] = $formatter->format($entry['timestamp']);

            unset($entry['datetext']);
            unset($entry['sales']);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getExportPortletData(ParameterBag $parameterBag)
    {
        return $this->getExportReportData($parameterBag);
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->totalRecords;
    }
}
