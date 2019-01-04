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
use CoreShop\Component\Core\Report\ExportReportInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class AbandonedCartsReport implements ReportInterface, ExportReportInterface
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
     * @var PimcoreRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var PimcoreRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @param RepositoryInterface        $storeRepository
     * @param Connection                 $db
     * @param PimcoreRepositoryInterface $cartRepository,
     * @param PimcoreRepositoryInterface $customerRepository
     * @param LocaleContextInterface     $localeContext
     */
    public function __construct(
        RepositoryInterface $storeRepository,
        Connection $db,
        PimcoreRepositoryInterface $cartRepository,
        PimcoreRepositoryInterface $customerRepository,
        LocaleContextInterface $localeContext
    ) {
        $this->storeRepository = $storeRepository;
        $this->db = $db;
        $this->cartRepository = $cartRepository;
        $this->customerRepository = $customerRepository;
        $this->localeContext = $localeContext;
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

        $maxToday = new Carbon();
        $minToday = new Carbon();

        //abandoned = 48h before today.
        $maxTo = $maxToday->subDay(2)->addSecond(1);
        $minFrom = $minToday->subDay(3);

        $page = $parameterBag->get('page', 1);
        $limit = $parameterBag->get('limit', 50);
        $offset = $parameterBag->get('offset', $page === 1 ? 0 : ($page - 1) * $limit);

        $userClassId = $this->customerRepository->getClassId();
        $cartClassId = $this->cartRepository->getClassId();

        $fromTimestamp = $from->getTimestamp();
        $toTimestamp = $to->getTimestamp();

        if ($from->gt($minFrom)) {
            $fromTimestamp = $minFrom->getTimestamp();
        }

        if ($to->gt($maxTo)) {
            $to = $maxTo;
            $toTimestamp = $to->getTimestamp();
        }

        if (is_null($storeId)) {
            return [];
        }

        $store = $this->storeRepository->find($storeId);
        if (!$store instanceof StoreInterface) {
            return [];
        }

        $sqlQuery = "SELECT SQL_CALC_FOUND_ROWS
                         cart.o_creationDate AS creationDate,
                         cart.o_modificationDate AS modificationDate,
                         cart.items,
                         cart.oo_id AS cartId,
                         `user`.email, CONCAT(`user`.firstname, ' ', `user`.lastname) AS userName,
                         `pg`.identifier AS selectedPayment
                        FROM object_$cartClassId AS cart
                        LEFT JOIN object_$userClassId AS `user` ON `user`.oo_id = cart.customer__id
                        LEFT JOIN coreshop_payment_provider AS `pg` ON `pg`.id = cart.paymentProvider
                        WHERE cart.items <> ''
                          AND cart.store = $storeId
                          AND cart.order__id IS NULL
                          AND cart.o_creationDate > ?
                          AND cart.o_creationDate < ?
                     GROUP BY cart.oo_id
                     ORDER BY cart.o_creationDate DESC
                     LIMIT $offset,$limit";

        $data = $this->db->fetchAll($sqlQuery, [$fromTimestamp, $toTimestamp]);
        $this->totalRecords = (int) $this->db->fetchColumn('SELECT FOUND_ROWS()');

        foreach ($data as &$entry) {
            $entry['itemsInCart'] = count(array_filter(explode(',', $entry['items'])));
            $entry['userName'] = empty($entry['userName']) ? '--' : $entry['userName'];
            $entry['email'] = empty($entry['email']) ? '--' : $entry['email'];
            $entry['selectedPayment'] = empty($entry['selectedPayment']) ? '--' : $entry['selectedPayment'];

            unset($entry['items']);
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
            $entry['creationDate'] = $formatter->format($entry['creationDate']);
            $entry['modificationDate'] = $formatter->format($entry['modificationDate']);
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
