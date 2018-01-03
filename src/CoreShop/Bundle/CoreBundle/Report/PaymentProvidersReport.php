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
use CoreShop\Component\Core\Model\PaymentProviderInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class PaymentProvidersReport implements ReportInterface
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
     * @var RepositoryInterface
     */
    private $paymentProviderRepository;

    /**
     * @var array
     */
    private $pimcoreClasses;

    /**
     * @param Connection $db
     * @param RepositoryInterface $paymentProviderRepository
     * @param array $pimcoreClasses
     */
    public function __construct(Connection $db, RepositoryInterface $paymentProviderRepository, array $pimcoreClasses)
    {
        $this->db = $db;
        $this->paymentProviderRepository = $paymentProviderRepository;
        $this->pimcoreClasses = $pimcoreClasses;
    }

    /**
     * {@inheritdoc}
     */
    public function getReportData(ParameterBag $parameterBag) {
        $fromFilter = $parameterBag->get('from' , strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);
        $fromTimestamp = $from->getTimestamp();
        $toTimestamp = $to->getTimestamp();

        $tableName = 'object_query_'.$this->pimcoreClasses['order'];;
        $sql = "
            SELECT  paymentProvider, 
                    COUNT(1) as total, 
                    COUNT(1) / t.cnt * 100 as `percentage` 
            FROM $tableName as `order` 
            INNER JOIN objects as o 
              ON o.o_id = `order`.oo_id 
            CROSS JOIN 
            (
              SELECT COUNT(1) as cnt 
              FROM $tableName as `order` 
              INNER JOIN objects as o 
                ON o.o_id = `order`.oo_id  
              WHERE o_creationDate > $fromTimestamp AND o_creationDate < $toTimestamp
            ) t 
          WHERE o_creationDate > $fromTimestamp AND o_creationDate < $toTimestamp 
          GROUP BY paymentProvider";

        $results = $this->db->fetchAll($sql);
        $data = [];
        
        foreach ($results as $result) {
            $paymentProvider = null;

            if ($result['paymentProvider']) {
                $paymentProvider = $this->paymentProviderRepository->find($result['paymentProvider']);
            }

            $data[] = [
                'provider' => $paymentProvider instanceof PaymentProviderInterface ? $paymentProvider->getName() : 'unkown',
                'data' => floatval($result['percentage']),
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
