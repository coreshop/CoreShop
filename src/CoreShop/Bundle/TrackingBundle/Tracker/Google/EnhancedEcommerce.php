<?php

namespace CoreShop\Bundle\TrackingBundle;

use CoreShop\Bundle\TrackingBundle\Builder\ItemBuilderInterface;
use CoreShop\Bundle\TrackingBundle\Model\ActionData;
use CoreShop\Bundle\TrackingBundle\Model\ImpressionData;
use CoreShop\Bundle\TrackingBundle\Model\ProductData;
use CoreShop\Bundle\TrackingBundle\Tracker\AbstractClientTracker;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Pimcore\Google\Analytics;
use Symfony\Component\Templating\EngineInterface;

class EnhancedEcommerce extends AbstractClientTracker
{
    /**
     * @var ItemBuilderInterface
     */
    public $itemBuilder;

    /**
     * EnhancedEcommerce constructor.
     * @param EngineInterface $renderer
     * @param ItemBuilderInterface $itemBuilder
     */
    public function __construct(EngineInterface $renderer, ItemBuilderInterface $itemBuilder)
    {
        parent::__construct($renderer);

        $this->itemBuilder = $itemBuilder;
    }

    public function init()
    {
        Analytics::addAdditionalCode("ga('require', 'ec')", "beforePageview");
    }

    /**
     * {@inheritdoc}
     */
    protected function render($viewName, $data = [])
    {
        $view = $this->track(["viewName" => $viewName, "data" => $data]);

        Analytics::addAdditionalCode($view, 'beforePageview');

        return $view;
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableView(PurchasableInterface $product)
    {
        $item = $this->getItemBuilder()->buildPurchasableViewItem($product);

        $productData = $this->transformProductAction($item);

        unset($productData['quantity']);
        unset($productData['price']);

        $this->render("product", ["productData" => $productData]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableImpression(PurchasableInterface $product)
    {
        $item = $this->getItemBuilder()->buildPurchasableImpressionItem($product);

        $productData = $this->transformProductImpression($item);

        $this->render("impression", ["productData" => $productData]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableActionAdd(PurchasableInterface $product, $quantity = 1)
    {
        $this->trackPurchasableAction($product, "add", $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableActionRemove(PurchasableInterface $product, $quantity = 1)
    {
        $this->trackPurchasableAction($product, "remove", $quantity);
    }

    /**
     * {@inheritdoc}
     */
    protected function trackPurchasableAction(PurchasableInterface $product, $action, $quantity = 1)
    {
        $item = $this->getItemBuilder()->buildPurchasableActionItem($product);
        $item->setQuantity($quantity);

        $productData = $this->transformProductAction($item);

        $this->render("action", ["productData" => $productData, "action" => $action]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckout(CartInterface $cart, $stepNumber = null, $checkoutOption = null)
    {
        $items = $this->getItemBuilder()->buildCheckoutItemsByCart($cart);
        $products = [];

        foreach ($items as $item) {
            $products[] = $this->transformProductAction($item);
        }

        $this->render("checkout", ["items" => $items, "products" => $products, "actionData" => ["step" => $stepNumber ? $stepNumber : 1]]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep(CartInterface $cart, $stepNumber = null, $checkoutOption = null)
    {
        $items = $this->getItemBuilder()->buildCheckoutItemsByCart($cart);

        $actionData = [];

        if ($stepNumber) {
            $actionData['step'] = $stepNumber;
        }

        if ($checkoutOption) {
            $actionData['option'] = $checkoutOption;
        }

        $this->render("checkout", ["items" => $items, "products" => [], "actionData" => $actionData]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutAction(CartInterface $cart, $stepNumber = null, $checkoutOption = null)
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

        $this->render("checkout", ["items" => $items, "products" => $products, "actionData" => $actionData]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutComplete(OrderInterface $order)
    {
        $orderItem = $this->getItemBuilder()->buildOrderAction($order);
        $items = $this->getItemBuilder()->buildCheckoutItems($order);

        $products = [];

        foreach ($items as $item) {
            $products[] = $this->transformProductAction($item);
        }

        $this->render("checkout-complete", ["items" => $items, "order" => $orderItem, "products" => $products]);
    }

    /**
     * @return ItemBuilderInterface
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
