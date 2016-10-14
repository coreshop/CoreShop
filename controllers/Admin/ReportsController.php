<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Model;
use CoreShop\Controller\Action\Admin;
use CoreShop\Helper\ReportQuery;

/**
 * Class CoreShop_Admin_ReportsController
 */
class CoreShop_Admin_ReportsController extends Admin
{
    public function getProductsReportAction()
    {
        $filters = $this->getParam('filters', array('from' => date('01-m-Y'), 'to' => date('m-t-Y')));
        $from = new \Pimcore\Date($filters['from']);
        $to = new \Pimcore\Date($filters['to']);

        $orderClassId = Model\Order::classId();
        $orderItemClassId = Model\Order\Item::classId();
        $productClassId = Model\Product::classId();

        $db = \Pimcore\Db::get();

        $query = "
            SELECT 
              orderItems.product__id,
              products.articleNumber as name,
              SUM(orderItems.retailPrice * orderItems.amount) as sales, 
              AVG(orderItems.retailPrice * orderItems.amount) as salesPrice,
              SUM((orderItems.retailPrice - orderItems.wholesalePrice) * orderItems.amount) as profit,
              COUNT(orderItems.product__id) as count
            FROM object_query_$orderClassId AS orders
            INNER JOIN object_relations_$orderClassId as orderRelations ON orderRelations.src_id = orders.oo_id AND orderRelations.fieldname = \"items\"
            INNER JOIN object_query_$orderItemClassId AS orderItems ON orderRelations.dest_id = orderItems.oo_id
            INNER JOIN object_query_$productClassId AS products ON orderItems.product__id = products.oo_id
            WHERE orders.orderDate > ? AND orders.orderDate < ? AND orderItems.product__id IS NOT NULL
            GROUP BY orderItems.product__id
            ORDER BY COUNT(orderItems.product__id) DESC
        ";

        $productSales = $db->fetchAll($query, array($from->getTimestamp(), $to->getTimestamp()));

        foreach ($productSales as &$sale) {
            $sale['salesPriceFormatted'] = \CoreShop::getTools()->formatPrice($sale['salesPrice']);
            $sale['salesFormatted'] = \CoreShop::getTools()->formatPrice($sale['sales']);
            $sale['profitFormatted'] = \CoreShop::getTools()->formatPrice($sale['profit']);
        }

        $this->_helper->json(array('data' => array_values($productSales)));
    }

    public function getCategoriesReportAction()
    {
        $filters = $this->getParam('filters', array('from' => date('01-m-Y'), 'to' => date('m-t-Y')));
        $from = new \Pimcore\Date($filters['from']);
        $to = new \Pimcore\Date($filters['to']);

        $orderClassId = Model\Order::classId();
        $orderItemClassId = Model\Order\Item::classId();
        $productClassId = Model\Product::classId();
        $categoryClassId = Model\Category::classId();
        $categoryLocalizedQuery = $categoryClassId . "_" . $this->getLanguage();

        $db = \Pimcore\Db::get();

        $query = "
            SELECT 
              category.oo_id as id,
              categoryLocalized.name,
              SUM(orderItems.retailPrice * orderItems.amount) as sales, 
              SUM((orderItems.retailPrice - orderItems.wholesalePrice) * orderItems.amount) as profit,
              COUNT(category.oo_id) as count
            FROM object_query_$orderClassId AS orders
            INNER JOIN object_relations_$orderClassId as orderRelations ON orderRelations.src_id = orders.oo_id AND orderRelations.fieldname = \"items\"
            INNER JOIN object_query_$orderItemClassId AS orderItems ON orderRelations.dest_id = orderItems.oo_id
            INNER JOIN object_query_$productClassId AS products ON orderItems.product__id = products.oo_id
            INNER JOIN object_relations_$productClassId as productRelations ON productRelations.src_id = products.oo_id AND productRelations.fieldname = \"categories\"
            INNER JOIN object_query_$categoryClassId as category ON productRelations.dest_id = category.oo_id
            INNER JOIN object_localized_query_$categoryLocalizedQuery as categoryLocalized ON categoryLocalized.ooo_id = category.oo_id
            WHERE orders.orderDate > ? AND orders.orderDate < ? AND orderItems.product__id IS NOT NULL
            GROUP BY category.oo_id
            ORDER BY COUNT(category.oo_id) DESC
        ";

        $catSales = $db->fetchAll($query, array($from->getTimestamp(), $to->getTimestamp()));

        foreach ($catSales as &$sale) {
            $sale['salesFormatted'] = \CoreShop::getTools()->formatPrice($sale['sales']);
            $sale['profitFormatted'] = \CoreShop::getTools()->formatPrice($sale['profit']);
        }

        $this->_helper->json(array('data' => array_values($catSales)));
    }

    public function getCustomersReportAction()
    {
        $filters = $this->getParam('filters', array('from' => date('01-m-Y'), 'to' => date('m-t-Y')));
        $from = new \Pimcore\Date($filters['from']);
        $to = new \Pimcore\Date($filters['to']);

        $orderClassId = Model\Order::classId();
        $customerClassId = Model\User::classId();

        $db = \Pimcore\Db::get();

        $query = "
            SELECT 
              customer.oo_id,
              customer.email as name,
              SUM(orders.total) as sales, 
              COUNT(customer.oo_id) as count
            FROM object_query_$orderClassId AS orders
            INNER JOIN object_query_$customerClassId AS customer ON orders.customer__id = customer.oo_id
            WHERE orders.orderDate > ? AND orders.orderDate < ? AND customer.oo_id IS NOT NULL
            GROUP BY customer.oo_id
            ORDER BY COUNT(customer.oo_id) DESC
        ";

        $custSales = $db->fetchAll($query, array($from->getTimestamp(), $to->getTimestamp()));

        foreach ($custSales as &$sale) {
            $sale['salesFormatted'] = \CoreShop::getTools()->formatPrice($sale['sales']);
        }

        $this->_helper->json(array('data' => array_values($custSales)));
    }

    public function getQuantitiesReportAction()
    {
        $page = $this->getParam("page", 1) - 1;
        $limit = $this->getParam("limit", 25);
        $offset = $page * $limit;

        $productClassId = Model\Product::classId();
        $productLocalizedClassId = $productClassId . "_" . $this->getLanguage();
        $sqlOrderBy = ReportQuery::getSqlSort($this->getAllParams());

        $db = \Pimcore\Db::get();

        $query = "
            SELECT * FROM (
                SELECT 
                  productsLocalized.name,
                  products.quantity,
                  products.retailPrice,
                  products.quantity * products.retailPrice as totalPrice
                FROM object_query_$productClassId AS products
                INNER JOIN object_localized_query_$productLocalizedClassId AS productsLocalized ON products.oo_id = productsLocalized.ooo_id
            ) as query
            $sqlOrderBy
            LIMIT $offset, $limit
        ";

        $totalQuery = "
            SELECT count(*) as count FROM object_query_$productClassId
        ";

        $products = $db->fetchAll($query);
        $total = $db->fetchCol($totalQuery);

        foreach($products as &$product) {
            $product['retailPriceFormatted'] = \CoreShop::getTools()->formatPrice($product['retailPrice']);
            $product['totalPriceFormatted'] = \CoreShop::getTools()->formatPrice($product['totalPrice']);
        }

        $this->_helper->json(array('data' => $products, "total" => $total));
    }

    /**
     * Return Orders/Carts from last 31 Days.
     */
    public function getOrdersCartsReportAction()
    {
        $filters = $this->getParam('filters', array('from' => date('01-m-Y'), 'to' => date('m-t-Y')));
        $from = new \Pimcore\Date($filters['from']);
        $to = new \Pimcore\Date($filters['to']);

        $orderClassId = Model\Order::classId();
        $cartClassId = Model\Cart::classId();

        $db = \Pimcore\Db::get();

        $queries = [];

        $fromTimestamp = $from->getTimestamp();
        $toTimestamp = $to->getTimestamp();

        foreach(['LEFT', 'RIGHT'] as $join) {
            $queries[] = "
                SELECT
                    CASE WHEN orderDateTimestamp IS NULL THEN cartDateTimestamp ELSE orderDateTimestamp END as timestamp,
                    CASE WHEN orderCount IS NULL THEN 0 ELSE orderCount END as orders,
                    CASE WHEN cartCount IS NULL THEN 0 ELSE cartCount END as carts
                FROM (
                  SELECT 
                    COUNT(*) as orderCount,
                    DATE(FROM_UNIXTIME(orderDate)) as orderDateTimestamp
                  FROM object_query_$orderClassId AS orders
                  WHERE orderDate > $fromTimestamp AND orderDate < $toTimestamp
                  GROUP BY DATE(FROM_UNIXTIME(orderDate))
                ) as ordersQuery
                $join OUTER JOIN (
                  SELECT
                    COUNT(*) as cartCount,
                    DATE(FROM_UNIXTIME(o_creationDate)) as cartDateTimestamp
                  FROM object_$cartClassId AS carts
                  WHERE o_creationDate > $fromTimestamp AND o_creationDate < $toTimestamp
                  GROUP BY DATE(FROM_UNIXTIME(o_creationDate))
                ) as cartsQuery ON cartsQuery.cartDateTimestamp = ordersQuery.orderDateTimestamp
            ";
        }

        $data = $db->fetchAll(implode(PHP_EOL . "UNION ALL" . PHP_EOL, $queries));

        foreach($data as &$day) {
            $date = new \Zend_Date($day['timestamp']);

            $day['datetext'] = $date->get(\Zend_Date::DATE_LONG);
        }

        $this->_helper->json(array('data' => $data));
    }

    /**
     * Return Sales from last 31 days.
     */
    public function getSalesReportAction()
    {
        $filters = $this->getParam('filters', array('from' => date('01-m-Y'), 'to' => date('m-t-Y')));
        $from = new \Pimcore\Date($filters['from']);
        $to = new \Pimcore\Date($filters['to']);

        $data = array();

        $classId = Model\Order::classId();
        $db = \Pimcore\Db::get();

        $sqlQuery = "SELECT DATE(FROM_UNIXTIME(orderDate)) as dayDate, orderDate, SUM(total) as total FROM object_query_$classId WHERE orderDate > ? AND orderDate < ? GROUP BY DATE(FROM_UNIXTIME(orderDate))";
        $results = $db->fetchAll($sqlQuery, array($from->getTimestamp(), $to->getTimestamp()));

        foreach($results as $result) {
            $date = new \Pimcore\Date($result['orderDate']);

            $data[] = array(
                'timestamp' => $date->getTimestamp(),
                'datetext' => $date->get(\Zend_Date::DATE_LONG),
                'sales' => $result['total'],
                'salesFormatted' => \CoreShop::getTools()->formatPrice($result['total'])
            );
        }

        $this->_helper->json(array('data' => $data));
    }

    public function getCarrierReportAction()
    {
        $filter = ReportQuery::extractFilterDefinition($this->getParam('filters'));

        $tableName = 'object_query_'.Model\Order::classId();
        $sql = "SELECT carrier, COUNT(1) as total, COUNT(1) / t.cnt * 100 as `percentage` FROM $tableName as `order` INNER JOIN objects as o ON o.o_id = `order`.oo_id CROSS JOIN (SELECT COUNT(1) as cnt FROM $tableName as `order` INNER JOIN objects as o ON o.o_id = `order`.oo_id  WHERE $filter) t WHERE $filter GROUP BY carrier";

        $db = \Pimcore\Db::get();
        $results = $db->fetchAll($sql);
        $data = array();

        foreach ($results as $result) {
            $carrier = Model\Carrier::getById($result['carrier']);

            $data[] = array(
                'carrier' => $carrier instanceof Model\Carrier ? $carrier->getName() : $result['carrier'],
                'data' => floatval($result['percentage']),
            );
        }

        $this->_helper->json(array('data' => $data));
    }

    public function getPaymentReportAction()
    {
        $filter = ReportQuery::extractFilterDefinition($this->getParam('filters'));

        $tableName = 'object_query_'.Model\Order::classId();
        $sql = "SELECT paymentProvider, COUNT(1) as total, COUNT(1) / t.cnt * 100 as `percentage` FROM $tableName as `order` INNER JOIN objects as o ON o.o_id = `order`.oo_id CROSS JOIN (SELECT COUNT(1) as cnt FROM $tableName as `order` INNER JOIN objects as o ON o.o_id = `order`.oo_id  WHERE $filter) t WHERE $filter GROUP BY paymentProvider";

        $db = \Pimcore\Db::get();
        $results = $db->fetchAll($sql);
        $data = array();

        foreach ($results as $result) {
            $data[] = array(
                'provider' => $result['paymentProvider'],
                'data' => floatval($result['percentage']),
            );
        }

        $this->_helper->json(array('data' => $data));
    }

    public function getEmptyCategoriesMonitoringAction()
    {
        $cats = Model\Category::getList();
        $cats = $cats->getObjects();

        $emptyCategories = array();

        foreach ($cats as $category) {
            $products = $category->getProducts(true);

            if (count($products) === 0) {
                $emptyCategories[] = array(
                    'name' => $category->getName(),
                    'id' => $category->getId(),
                );
            }
        }

        $this->_helper->json(array('data' => $emptyCategories));
    }

    public function getDisabledProductsMonitoringAction()
    {
        $products = Model\Product::getList();
        $products->setCondition('enabled=? OR availableForOrder=?', array(0, 0));

        $result = array();

        foreach ($products as $product) {
            $result[] = array(
                'id' => $product->getId(),
                'name' => $product->getName(),
                'enabled' => $product->getEnabled(),
                'availableForOrder' => $product->getAvailableForOrder(),
            );
        }

        $this->_helper->json(array('data' => $result));
    }

    public function getOutOfStockProductsMonitoringAction()
    {
        $query = '((quantity <= 0 OR quantity IS NULL) AND outOfStockBehaviour=0)';

        $defaultOutOfStockBehaviour = Model\Configuration::get('SYSTEM.STOCK.DEFAULTOUTOFSTOCKBEHAVIOUR');

        if ($defaultOutOfStockBehaviour === Model\Product::OUT_OF_STOCK_DENY) {
            $query .= ' OR ((quantity <= 0 OR quantity IS NULL) AND (outOfStockBehaviour='.Model\Product::OUT_OF_STOCK_DENY.') OR outOfStockBehaviour IS NULL)';
        }

        $products = Model\Product::getList();
        $products->setCondition($query);

        $result = array();

        $behaviour = array(
            0 => 'deny',
            1 => 'allow',
        );

        foreach ($products as $product) {
            $productBehaviour = $product->getOutOfStockBehaviour();

            if ($productBehaviour === null || $productBehaviour === Model\Product::OUT_OF_STOCK_DEFAULT) {
                $productBehaviour = $defaultOutOfStockBehaviour;
            }

            $result[] = array(
                'id' => $product->getId(),
                'name' => $product->getName(),
                'quantity' => $product->getQuantity(),
                'outOfStockBehaviour' => $behaviour[$productBehaviour],
            );
        }

        $this->_helper->json(array('data' => $result));
    }
}
