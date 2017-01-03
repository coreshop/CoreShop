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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Tracking\Google\TagManager;

use CoreShop\Tracking\Google\TagManager\Controller\Plugin;
use CoreShop\Model\Cart;
use CoreShop\Model\Order;
use CoreShop\Model\Product;
use CoreShop\Tracking\ActionData;
use CoreShop\Tracking\ClientTracker;
use CoreShop\Tracking\Google\ItemBuilder;
use CoreShop\Tracking\ImpressionData;
use CoreShop\Tracking\ProductData;

/**
 * Class EnhancedEcommerce
 * @package CoreShop\Tracking\Google\TagManager
 */
class EnhancedEcommerce extends ClientTracker
{

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
    public function init()
    {
        $frontController = \Zend_Controller_Front::getInstance();

        $frontController->registerPlugin(new Plugin());

        $plugin = $frontController->getPlugin('Pimcore\Controller\Plugin\GoogleTagManager');

        if ($plugin) {
            $plugin->disable();
        }
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function track($data)
    {
        Plugin::addDataLayer($data);
    }

    /**
     * @param Product $product
     * @return mixed
     */
    public function trackProductView(Product $product)
    {
        $item = $this->getItemBuilder()->buildProductViewItem($product);

        $productData = $this->transformProductAction($item);

        $this->track(["detail" => ['products' => [$productData]]]);
    }

    /**
     * @param Product $product
     * @return mixed
     */
    public function trackProductImpression(Product $product)
    {
        $item = $this->getItemBuilder()->buildProductImpressionItem($product);

        $productData = $this->transformProductImpression($item);

        $this->track(["impressions" => [$productData]]);
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return mixed
     */
    public function trackProductActionAdd(Product $product, $quantity = 1)
    {
        $this->trackProductAction($product, "add", $quantity);
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return mixed
     */
    public function trackProductActionRemove(Product $product, $quantity = 1)
    {
        $this->trackProductAction($product, "remove", $quantity);
    }

    /**
     * @param Product $product
     * @param $action
     * @param int $quantity
     */
    protected function trackProductAction(Product $product, $action, $quantity = 1)
    {
        $item = $this->getItemBuilder()->buildProductActionItem($product);
        $item->setQuantity($quantity);

        $productData = $this->transformProductAction($item);

        $this->track([$action => ["products" => [$productData]]]);
    }

    /**
     * @param Cart $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     * @return mixed
     */
    public function trackCheckout(Cart $cart, $stepNumber = null, $checkoutOption = null)
    {
        $items = $this->getItemBuilder()->buildCheckoutItemsByCart($cart);
        $products = [];

        foreach ($items as $item) {
            $products[] = $this->transformProductAction($item);
        }

        $this->track(
            ["checkout" => [
                "actionField" => [
                    "step" => $stepNumber ? $stepNumber : 1,
                    'option' => $checkoutOption
                ],
                "products" => $products
            ]]
        );
    }

    /**
     * @param Cart $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     * @return mixed
     */
    public function trackCheckoutStep(Cart $cart, $stepNumber = null, $checkoutOption = null)
    {
        $items = $this->getItemBuilder()->buildCheckoutItemsByCart($cart);

        $actionData = [];

        if ($stepNumber) {
            $actionData['step'] = $stepNumber;
        }

        if ($checkoutOption) {
            $actionData['option'] = $checkoutOption;
        }

        $this->track(
            ["checkout_option" => [
                "actionField" => [
                    "step" => $stepNumber ? $stepNumber : 1,
                    'option' => $checkoutOption
                ]
            ]]
        );
    }

    /**
     * @param Cart $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     * @return mixed
     */
    public function trackCheckoutAction(Cart $cart, $stepNumber = null, $checkoutOption = null)
    {
        $items = $this->getItemBuilder()->buildCheckoutItemsByCart($cart);

        $actionData = [];
        $products = [];

        if ($stepNumber) {
            $actionData['step'] = $stepNumber;
        }

        if ($checkoutOption) {
            $actionData['option'] = $checkoutOption;
        }

        foreach ($items as $item) {
            $products[] = $this->transformProductAction($item);
        }

        $this->track(
            ["checkout_option" => [
                "actionField" => [
                    "step" => $stepNumber ? $stepNumber : 1,
                    'option' => $checkoutOption
                ]
            ]]
        );
    }

    /**
     * @param Order $order
     * @return mixed
     */
    public function trackCheckoutComplete(Order $order)
    {
        $orderItem = $this->getItemBuilder()->buildOrderAction($order);
        $items = $this->getItemBuilder()->buildCheckoutItems($order);

        $products = [];

        foreach ($items as $item) {
            $products[] = $this->transformProductAction($item);
        }

        $this->track([
            "purchase" => [
                "actionField" => $orderItem,
                "products" => $products
            ]
        ]);
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
