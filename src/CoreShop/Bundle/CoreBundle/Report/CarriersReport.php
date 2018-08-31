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
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class CarriersReport implements ReportInterface
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
     * @var RepositoryInterface
     */
    private $carrierRepository;

    /**
     * @var PimcoreRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param RepositoryInterface $storeRepository
     * @param Connection $db
     * @param RepositoryInterface $carrierRepository
     * @param PimcoreRepositoryInterface $orderRepository
     */
    public function __construct(
        RepositoryInterface $storeRepository,
        Connection $db,
        RepositoryInterface $carrierRepository,
        PimcoreRepositoryInterface $orderRepository
    )
    {
        $this->storeRepository = $storeRepository;
        $this->db = $db;
        $this->carrierRepository = $carrierRepository;
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

        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);
        $fromTimestamp = $from->getTimestamp();
        $toTimestamp = $to->getTimestamp();

        if (is_null($storeId)) {
            return [];
        }

        $store = $this->storeRepository->find($storeId);
        if (!$store instanceof StoreInterface) {
            return [];
        }

        $tableName = 'object_query_'.$this->orderRepository->getClassId();
        $sql = "
              SELECT carrier, 
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
                  WHERE store = $storeId AND o_creationDate > $fromTimestamp AND o_creationDate < $toTimestamp
                ) t 
              WHERE store = $storeId AND carrier IS NOT NULL AND o_creationDate > $fromTimestamp AND o_creationDate < $toTimestamp GROUP BY carrier";

        $results = $this->db->fetchAll($sql);
        $data = [];

        foreach ($results as $result) {
            $carrier = null;

            if ($result['carrier']) {
                $carrier = $this->carrierRepository->find($result['carrier']);
            }

            $data[] = [
                'carrier' => $carrier instanceof CarrierInterface ? $carrier->getIdentifier() : $result['carrier'],
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
