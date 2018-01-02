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
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Doctrine\DBAL\Connection;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Model\User;
use Symfony\Component\HttpFoundation\ParameterBag;

class ProductsReport implements ReportInterface
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
     * @var TokenStorageUserResolver
     */
    private $tokenStorageUserResolver;

    /**
     * @var TranslationLocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @var array
     */
    private $pimcoreClasses;

    /**
     * @var array
     */
    private $productImplementations;

    /**
     * @param Connection                         $db
     * @param TokenStorageUserResolver           $tokenStorageUserResolver
     * @param TranslationLocaleProviderInterface $localeProvider
     * @param MoneyFormatterInterface            $moneyFormatter
     * @param array                              $pimcoreClasses
     * @param array                              $productImplementations
     */
    public function __construct(
        Connection $db,
        TokenStorageUserResolver $tokenStorageUserResolver,
        TranslationLocaleProviderInterface $localeProvider,
        MoneyFormatterInterface $moneyFormatter,
        array $pimcoreClasses,
        array $productImplementations
    ) {
        $this->db = $db;
        $this->tokenStorageUserResolver = $tokenStorageUserResolver;
        $this->localeProvider = $localeProvider;
        $this->moneyFormatter = $moneyFormatter;
        $this->pimcoreClasses = $pimcoreClasses;
        $this->productImplementations = $productImplementations;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(ParameterBag $parameterBag)
    {
        $fromFilter = $parameterBag->get('from', strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $objectTypeFilter = $parameterBag->get('objectType', 'all');
        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $page = $parameterBag->get('page', 1);
        $limit = $parameterBag->get('limit', 50);
        $offset = $parameterBag->get('offset', $page === 1 ? 0 : ($page - 1) * $limit);

        $orderClassId = $this->pimcoreClasses['order'];
        $orderItemClassId = $this->pimcoreClasses['order_item'];

        $localizedTableLanguage = $this->getLanguageForLocalizedTable();

        if ($objectTypeFilter === 'container') {

            $objectClassArray = [];
            foreach ($this->productImplementations as $productClass) {
                $obj = new $productClass();
                $objectClassArray[] = $obj->getClassId();
            }

            $unionData = [];
            foreach ($objectClassArray as $id) {
                $unionData[] = 'SELECT `o_id`, `name` FROM object_localized_' . $id . '_' . $localizedTableLanguage;
            }

            $union = join(' UNION ALL ', $unionData);

            $query = "
              SELECT SQL_CALC_FOUND_ROWS
                products.o_id as productId,
                products.`name` as productName,
                SUM(orderItems.itemRetailPriceNet * orderItems.quantity) AS sales, 
                AVG(orderItems.itemRetailPriceNet * orderItems.quantity) AS salesPrice,
                SUM((orderItems.itemRetailPriceNet - orderItems.itemWholesalePrice) * orderItems.quantity) AS profit,
                SUM(orderItems.quantity) AS `quantityCount`,
                COUNT(order.oo_id) AS `orderCount`
                FROM ($union) AS products
                INNER JOIN object_query_$orderItemClassId AS orderItems ON products.o_id = orderItems.mainObjectId
                INNER JOIN object_relations_$orderClassId AS orderRelations ON orderRelations.dest_id = orderItems.oo_id AND orderRelations.fieldname = \"items\"
                INNER JOIN object_query_$orderClassId AS `order` ON `order`.oo_id = orderRelations.src_id
                INNER JOIN element_workflow_state AS orderState ON `order`.oo_id = orderState.cid 
                WHERE orderState.ctype = 'object' AND orderState.state = 'complete' AND `order`.orderDate > ? AND `order`.orderDate < ?
                GROUP BY products.o_id
            LIMIT $offset,$limit";

        } else {

            $productTypeCondition = '1=1';
            if ($objectTypeFilter === 'object') {
                $productTypeCondition = 'orderItems.mainObjectId = NULL';
            } elseif ($objectTypeFilter === 'variant') {
                $productTypeCondition = 'orderItems.mainObjectId IS NOT NULL';
            }

            $query = "
                SELECT SQL_CALC_FOUND_ROWS
                  orderItems.objectId as productId,
                  orderItemsTranslated.name AS `productName`,
                  SUM(orderItems.itemRetailPriceNet * orderItems.quantity) AS sales, 
                  AVG(orderItems.itemRetailPriceNet * orderItems.quantity) AS salesPrice,
                  SUM((orderItems.itemRetailPriceNet - orderItems.itemWholesalePrice) * orderItems.quantity) AS profit,
                  SUM(orderItems.quantity) AS `quantityCount`,
                  COUNT(orderItems.objectId) AS `orderCount`
                FROM object_query_$orderClassId AS orders
                INNER JOIN object_relations_$orderClassId AS orderRelations ON orderRelations.src_id = orders.oo_id AND orderRelations.fieldname = \"items\"
                INNER JOIN object_query_$orderItemClassId AS orderItems ON orderRelations.dest_id = orderItems.oo_id
                INNER JOIN object_localized_query_" . $orderItemClassId . "_" . $localizedTableLanguage . " AS orderItemsTranslated ON orderItems.oo_id = orderItemsTranslated.ooo_id
                INNER JOIN element_workflow_state AS orderState ON orders.oo_id = orderState.cid 
                WHERE $productTypeCondition AND orderState.ctype = 'object' AND orderState.state = 'complete' AND orders.orderDate > ? AND orders.orderDate < ?
                GROUP BY orderItems.objectId
                ORDER BY orderCount DESC
                LIMIT $offset,$limit";
        }

        $productSales = $this->db->fetchAll($query, [$from->getTimestamp(), $to->getTimestamp()]);

        $this->totalRecords = (int)$this->db->fetchOne('SELECT FOUND_ROWS()');

        foreach ($productSales as &$sale) {
            $sale['salesPriceFormatted'] = $this->moneyFormatter->format($sale['salesPrice'], 'EUR');
            $sale['salesFormatted'] = $this->moneyFormatter->format($sale['sales'], 'EUR');
            $sale['profitFormatted'] = $this->moneyFormatter->format($sale['profit'], 'EUR');
            $sale['name'] = $sale['productName'] . ' (Id: ' . $sale['productId'] . ')';
        }

        return array_values($productSales);
    }

    /**
     * Get a valid frontend language which is also the user backend language.
     * If current users language is not available in backend, use the first valid frontend language.
     *
     * @return bool|string
     */
    public function getLanguageForLocalizedTable()
    {
        $backendLanguage = null;
        $localizedTableLanguage = null;

        $frontendLanguages = $this->localeProvider->getDefinedLocalesCodes();
        $user = $this->tokenStorageUserResolver->getUser();

        if ($user instanceof User) {
            $backendLanguage = $user->getLanguage();
        }

        // no frontend language defined. this should never happen.
        if (empty($frontendLanguages)) {
            return false;
        }

        if (!empty($backendLanguage) && in_array($backendLanguage, $frontendLanguages)) {
            $localizedTableLanguage = strtolower($backendLanguage);
        } else {
            $first = reset($frontendLanguages);
            $localizedTableLanguage = strtolower($first);
        }

        return $localizedTableLanguage;

    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->totalRecords;
    }
}
