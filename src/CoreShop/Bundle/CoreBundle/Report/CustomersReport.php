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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Report;

use Carbon\Carbon;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class CustomersReport implements ReportInterface
{
    private int $totalRecords = 0;
    private Connection $db;
    private MoneyFormatterInterface $moneyFormatter;
    private LocaleContextInterface $localeContext;
    private PimcoreRepositoryInterface $orderRepository;
    private PimcoreRepositoryInterface $customerRepository;

    public function __construct(
        Connection $db,
        MoneyFormatterInterface $moneyFormatter,
        LocaleContextInterface $localeContext,
        PimcoreRepositoryInterface $orderRepository,
        PimcoreRepositoryInterface $customerRepository
    ) {
        $this->db = $db;
        $this->moneyFormatter = $moneyFormatter;
        $this->localeContext = $localeContext;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
    }

    public function getReportData(ParameterBag $parameterBag): array
    {
        $fromFilter = $parameterBag->get('from', strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $page = $parameterBag->get('page', 1);
        $limit = $parameterBag->get('limit', 25);
        $offset = $parameterBag->get('offset', $page === 1 ? 0 : ($page - 1) * $limit);

        $orderClassId = $this->orderRepository->getClassId();
        $customerClassId = $this->customerRepository->getClassId();
        $orderCompleteState = OrderStates::STATE_COMPLETE;

        $query = "
            SELECT SQL_CALC_FOUND_ROWS
              customer.oo_id,
              customer.email as `emailAddress`,
              SUM(orders.totalNet) as sales, 
              COUNT(customer.oo_id) as `orderCount`
            FROM object_query_$orderClassId AS orders
            INNER JOIN object_query_$customerClassId AS customer ON orders.customer__id = customer.oo_id
            WHERE  orders.orderState = '$orderCompleteState' AND orders.orderDate > ? AND orders.orderDate < ? AND customer.oo_id IS NOT NULL
            GROUP BY customer.oo_id
            ORDER BY COUNT(customer.oo_id) DESC
            LIMIT $offset,$limit";

        $results = $this->db->fetchAllAssociative($query, [$from->getTimestamp(), $to->getTimestamp()]);
        $this->totalRecords = (int) $this->db->fetchColumn('SELECT FOUND_ROWS()');

        foreach ($results as &$result) {
            $result['salesFormatted'] = $this->moneyFormatter->format(
                $result['sales'],
                'EUR',
                $this->localeContext->getLocaleCode()
            );
        }

        return array_values($results);
    }

    public function getTotal(): int
    {
        return $this->totalRecords;
    }
}
