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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Tracking\Google\Analytics;

use CoreShop\Model\Cart;
use CoreShop\Model\Order;
use CoreShop\Model\Product;
use CoreShop\Tracking\ActionData;
use CoreShop\Tracking\ClientTracker;
use CoreShop\Tracking\Google\ItemBuilder;
use CoreShop\Tracking\ImpressionData;
use CoreShop\Tracking\ProductData;
use Pimcore\Google\Analytics;

/**
 * Class EnhancedEcommerce
 * @package CoreShop\Tracking\Google\Analytics
 */
class EnhancedEcommerce extends ClientTracker {

    /**
     * @var ItemBuilder
     */
    public $itemBuilder;

    /**
     * EnhancedEcommerce constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->itemBuilder = new ItemBuilder();
    }

    /**
     */
    public function init() {
        Analytics::addAdditionalCode("ga('require', 'ec')", "beforePageview");
    }

    /**
     * @param $viewName
     * @param $data
     *
     * @return string
     */
    protected function render($viewName, $data = [])
    {
        $view = $this->track(array("viewName" => $viewName, "data" => $data));

        Analytics::addAdditionalCode($view, 'beforePageview');

        return $view;
    }

    /**
     * @param Product $product
     * @return mixed
     */
    public function trackProductView(Product $product) {
        $item = $this->getItemBuilder()->buildProductViewItem($product);

        $productData = $this->transformProductAction($item);

        unset($productData['quantity']);
        unset($productData['price']);

        $this->render("product", array("productData" => $productData));
    }

    /**
     * @param Product $product
     * @return mixed
     */
    public function trackProductImpression(Product $product) {
        $item = $this->getItemBuilder()->buildProductImpressionItem($product);

        $productData = $this->transformProductImpression($item);

        $this->render("impression", array("productData" => $productData));
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return mixed
     */
    public function trackProductActionAdd(Product $product, $quantity = 1) {
        $this->trackProductAction($product, "add", $quantity);
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return mixed
     */
    public function trackProductActionRemove(Product $product, $quantity = 1) {
        $this->trackProductAction($product, "remove", $quantity);
    }

    /**
     * @param Product $product
     * @param $action
     * @param int $quantity
     */
    protected function trackProductAction(Product $product, $action, $quantity = 1) {
        $item = $this->getItemBuilder()->buildProductActionItem($product);
        $item->setQuantity($quantity);

        $productData = $this->transformProductAction($item);

        $this->render("action", array("productData" => $productData, "action" => $action));
    }

    /**
     * @param Cart $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     * @return mixed
     */
    public function trackCheckout(Cart $cart, $stepNumber = null, $checkoutOption = null) {
        $items = $this->getItemBuilder()->buildCheckoutItemsByCart($cart);
        $products = [];

        foreach($items as $item) {
            $products[] = $this->transformProductAction($item);
        }

        $this->render("checkout", array("items" => $items, "products" => $products, "actionData" => ["step" => $stepNumber ? $stepNumber : 1]));
    }

    /**
     * @param Cart $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     * @return mixed
     */
    public function trackCheckoutStep(Cart $cart, $stepNumber = null, $checkoutOption = null) {
        $items = $this->getItemBuilder()->buildCheckoutItemsByCart($cart);

        $actionData = [];

        if($stepNumber) {
            $actionData['step'] = $stepNumber;
        }

        if($checkoutOption) {
            $actionData['option'] = $checkoutOption;
        }

        $this->render("checkout", array("items" => $items, "products" => [], "actionData" => $actionData));
    }

    /**
     * @param Cart $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     * @return mixed
     */
    public function trackCheckoutAction(Cart $cart, $stepNumber = null, $checkoutOption = null) {
        $items = $this->getItemBuilder()->buildCheckoutItemsByCart($cart);

        $actionData = [];
        $products = [];

        if($stepNumber) {
            $actionData['step'] = $stepNumber;
        }

        if($checkoutOption) {
            $actionData['option'] = $checkoutOption;
        }

        foreach($items as $item) {
            $products[] = $this->transformProductAction($item);
        }

        $this->render("checkout", array("items" => $items, "products" => $products, "actionData" => $actionData));
    }

    /**
     * @param Order $order
     * @return mixed
     */
    public function trackCheckoutComplete(Order $order) {
        $orderItem = $this->getItemBuilder()->buildOrderAction($order);
        $items = $this->getItemBuilder()->buildCheckoutItems($order);

        $products = [];

        foreach($items as $item) {
            $products[] = $this->transformProductAction($item);
        }

        $this->render("checkout-complete", array("items" => $items, "order" => $orderItem, "products" => $products));
    }

    /**
     * @return ItemBuilder
     */
    public function getItemBuilder()
    {
        return $this->itemBuilder;
    }

    /**
     * Transform ActionData into classic analytics data array
     *
     * @param ActionData $actionData
     * @return array
     */
    protected function transformOrder(ActionData $actionData)
    {
        return [
            'id' => $actionData->getId(),
            'affilation' => $actionData->getAffiliation() ? : '',
            'revenue' => $actionData->getRevenue(),
            'tax' => $actionData->getTax(),
            'shipping' => $actionData->getShipping()
        ];
    }

    /**
     * Transform product action into enhanced data object
     *
     * @param ProductData $item
     * @return array
     */
    protected function transformProductAction(ProductData $item)
    {
        return $this->filterNullValues([
            'id' => $item->getId(),
            'name' => $item->getName(),
            'category' => $item->getCategory(),
            'brand' => $item->getBrand(),
            'variant' => $item->getVariant(),
            'price' => round($item->getPrice(), 2),
            'quantity' => $item->getQuantity() ?: 1,
            'position' => $item->getPosition(),
            'coupon' => $item->getCoupon()
        ]);
    }

    /**
     * Transform product action into enhanced data object
     *
     * @param ImpressionData $item
     * @return array
     */
    protected function transformProductImpression(ImpressionData $item)
    {
        return $this->filterNullValues([
            'id' => $item->getId(),
            'name' => $item->getName(),
            'category' => $item->getCategory(),
            'brand' => $item->getBrand(),
            'variant' => $item->getVariant(),
            'price' => round($item->getPrice(), 2),
            'list' => $item->getList(),
            'position' => $item->getPosition()
        ]);
    }
}