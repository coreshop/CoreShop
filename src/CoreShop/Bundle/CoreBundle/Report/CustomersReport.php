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
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class CustomersReport implements ReportInterface
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
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var PimcoreRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var PimcoreRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param Connection                 $db
     * @param MoneyFormatterInterface    $moneyFormatter
     * @param LocaleContextInterface     $localeContext
     * @param PimcoreRepositoryInterface $orderRepository
     * @param PimcoreRepositoryInterface $customerRepository
     */
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

    /**
     * {@inheritdoc}
     */
    public function getReportData(ParameterBag $parameterBag)
    {
        $fromFilter = $parameterBag->get('from', strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $orderClassId = $this->orderRepository->getClassId();
        $customerClassId = $this->customerRepository->getClassId();
        $orderCompleteState = OrderStates::STATE_COMPLETE;

        $query = "
            SELECT 
              customer.oo_id,
              customer.email as `name`,
              SUM(orders.totalNet) as sales, 
              COUNT(customer.oo_id) as `orderCount`
            FROM object_query_$orderClassId AS orders
            INNER JOIN object_query_$customerClassId AS customer ON orders.customer__id = customer.oo_id
            WHERE  orders.orderState = '$orderCompleteState' AND orders.orderDate > ? AND orders.orderDate < ? AND customer.oo_id IS NOT NULL
            GROUP BY customer.oo_id
            ORDER BY COUNT(customer.oo_id) DESC
        ";

        $customerSales = $this->db->fetchAll($query, [$from->getTimestamp(), $to->getTimestamp()]);
        foreach ($customerSales as &$sale) {
            $sale['salesFormatted'] = $this->moneyFormatter->format(
                $sale['sales'],
                'EUR',
                $this->localeContext->getLocaleCode()
            );
        }

        return array_values($customerSales);
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->totalRecords;
    }
}
