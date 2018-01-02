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
use CoreShop\Component\Resource\Translation\Provider\PimcoreTranslationLocaleProvider;
use Doctrine\DBAL\Connection;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Model\User;
use Symfony\Component\HttpFoundation\ParameterBag;

class ProductsReport implements ReportInterface
{
    /**
     * @var Connection
     */
    private $db;

    /**
     * @var TokenStorageUserResolver
     */
    private $tokenStorageUserResolver;

    /**
     * @var PimcoreTranslationLocaleProvider
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
     * @param Connection                       $db
     * @param TokenStorageUserResolver         $tokenStorageUserResolver
     * @param PimcoreTranslationLocaleProvider $localeProvider
     * @param MoneyFormatterInterface          $moneyFormatter
     * @param array                            $pimcoreClasses
     */
    public function __construct(
        Connection $db,
        TokenStorageUserResolver $tokenStorageUserResolver,
        PimcoreTranslationLocaleProvider $localeProvider,
        MoneyFormatterInterface $moneyFormatter,
        array $pimcoreClasses
    ) {
        $this->db = $db;
        $this->tokenStorageUserResolver = $tokenStorageUserResolver;
        $this->localeProvider = $localeProvider;
        $this->moneyFormatter = $moneyFormatter;
        $this->pimcoreClasses = $pimcoreClasses;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(ParameterBag $parameterBag)
    {
        $fromFilter = $parameterBag->get('from', strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $orderClassId = $this->pimcoreClasses['order'];
        $orderItemClassId = $this->pimcoreClasses['order_item'];

        $localizedTableLanguage = $this->getLanguageForLocalizedTable();

        $query = "
            SELECT 
              orderItems.product__id,
              orderItemsTranslated.name AS name,
              SUM(orderItems.itemRetailPriceNet * orderItems.quantity) AS sales, 
              AVG(orderItems.itemRetailPriceNet * orderItems.quantity) AS salesPrice,
              SUM((orderItems.itemRetailPriceNet - orderItems.itemWholesalePrice) * orderItems.quantity) AS profit,
              COUNT(orderItems.product__id) AS count
            FROM object_query_$orderClassId AS orders
            INNER JOIN object_relations_$orderClassId AS orderRelations ON orderRelations.src_id = orders.oo_id AND orderRelations.fieldname = \"items\"
            INNER JOIN object_query_$orderItemClassId AS orderItems ON orderRelations.dest_id = orderItems.oo_id
            INNER JOIN object_localized_query_" . $orderItemClassId . "_" . $localizedTableLanguage . " AS orderItemsTranslated ON orderItems.oo_id = orderItemsTranslated.ooo_id
            INNER JOIN element_workflow_state AS orderState ON orders.oo_id = orderState.cid 
            WHERE orderState.ctype = 'object' AND orderState.state = 'complete' AND orders.orderDate > ? AND orders.orderDate < ? AND orderItems.product__id IS NOT NULL
            GROUP BY orderItems.product__id
            ORDER BY COUNT(orderItems.product__id) DESC
        ";

        $productSales = $this->db->fetchAll($query, [$from->getTimestamp(), $to->getTimestamp()]);

        foreach ($productSales as &$sale) {
            $sale['salesPriceFormatted'] = $this->moneyFormatter->format($sale['salesPrice'], 'EUR');
            $sale['salesFormatted'] = $this->moneyFormatter->format($sale['sales'], 'EUR');
            $sale['profitFormatted'] = $this->moneyFormatter->format($sale['profit'], 'EUR');
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
}
