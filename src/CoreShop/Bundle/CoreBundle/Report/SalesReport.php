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

class SalesReport implements ReportInterface
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
     * @var array
     */
    private $pimcoreClasses;

    /**
     * @param Connection $db
     * @param MoneyFormatterInterface $moneyFormatter
     * @param array $pimcoreClasses
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
    public function getData(ParameterBag $parameterBag) {
        $groupBy = $parameterBag->get('groupBy', 'day');
        $fromFilter = $parameterBag->get('from' , strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $classId = $this->pimcoreClasses['order'];

        $data = [];

        $dateFormatter = null;
        $groupSelector = '';

        switch ($groupBy) {
            case 'day':
                $dateFormatter = 'd-m-Y';
                $groupSelector = 'DATE(FROM_UNIXTIME(orderDate))';
                break;
            case 'month':
                $dateFormatter = 'F Y';
                $groupSelector = 'MONTH(FROM_UNIXTIME(orderDate))';
                break;
            case 'year':
                $dateFormatter = 'Y';
                $groupSelector = 'YEAR(FROM_UNIXTIME(orderDate))';
                break;

        }

        $sqlQuery = "SELECT DATE(FROM_UNIXTIME(orderDate)) as dayDate, orderDate, SUM(totalNet) as total FROM object_query_$classId WHERE orderDate > ? AND orderDate < ? GROUP BY " . $groupSelector;
        $results = $this->db->fetchAll($sqlQuery, [$from->getTimestamp(), $to->getTimestamp()]);

        foreach ($results as $result) {
            $date = Carbon::createFromTimestamp($result['orderDate']);

            $data[] = [
                'timestamp' => $date->getTimestamp(),
                'datetext' => $date->format($dateFormatter),
                'sales' => $result['total'],
                'salesFormatted' => $this->moneyFormatter->format($result['total'], 'EUR')
            ];
        }

        return array_values($data);
    }
}
