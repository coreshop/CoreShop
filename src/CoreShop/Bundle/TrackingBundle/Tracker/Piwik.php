<?php

namespace CoreShop\Bundle\TrackingBundle\Tracker;

use CoreShop\Bundle\TrackingBundle\Builder\ItemBuilderInterface;
use CoreShop\Bundle\TrackingBundle\Model\ActionData;
use CoreShop\Bundle\TrackingBundle\Model\ImpressionData;
use CoreShop\Bundle\TrackingBundle\Model\ProductData;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Pimcore\Analytics\Piwik\Tracker as PiwikTracker;
use Symfony\Component\Templating\EngineInterface;

final class Piwik extends AbstractClientTracker
{
    /**
     * @var ItemBuilderInterface
     */
    private $itemBuilder;

    /**
     * @var PiwikTracker
     */
    private $tracker;

    /**
     * @param EngineInterface $renderer
     * @param PiwikTracker $tracker
     * @param ItemBuilderInterface $itemBuilder
     */
    public function __construct(EngineInterface $renderer, PiwikTracker $tracker, ItemBuilderInterface $itemBuilder)
    {
        parent::__construct($renderer);

        $this->itemBuilder = $itemBuilder;
        $this->tracker = $tracker;
    }

    /**
     * {@inheritdoc}
     */
    protected function render($data = [])
    {
        $view = $this->track(["viewName" => "calls", "data" => ["calls" => $data]]);

        $this->tracker->addCodePart($view, PiwikTracker::BLOCK_BEFORE_TRACK);

        return $view;
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableView(PurchasableInterface $product)
    {
        $item = $this->getItemBuilder()->buildPurchasableViewItem($product);

        $this->render([
            [
                'setEcommerceView',
                $item->getId(),
                $item->getName(),
                $item->getCategory(),
                $item->getPrice()
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableImpression(PurchasableInterface $product)
    {
        $item = $this->getItemBuilder()->buildPurchasableImpressionItem($product);

        $this->render([
            [
                'setEcommerceView',
                $item->getId(),
                $item->getName(),
                $item->getCategory(),
                $item->getPrice()
            ]
        ]);
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

        $this->render([
            $action . 'ecommerceItem',
            $item->getId(),
            $item->getName(),
            $item->getCategory(),
            $item->getPrice(),
            $item->getQuantity()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckout(CartInterface $cart, $stepNumber = null, $checkoutOption = null)
    {
        $items = $this->getItemBuilder()->buildCheckoutItemsByCart($cart);
        $calls = $this->buildItemCalls($items);

        $calls[] = [
            'trackEcommerceCheckout',
            $cart->getId(),
            $cart->getTotal(),
            $cart->getSubtotal(),
            $cart->getTotalTax()
        ];

        $this->render($calls);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep(CartInterface $cart, $stepNumber = null, $checkoutOption = null)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutAction(CartInterface $cart, $stepNumber = null, $checkoutOption = null)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutComplete(OrderInterface $order)
    {
        $items = $this->getItemBuilder()->buildCheckoutItems($order);
        $calls = $this->buildItemCalls($items);

        $calls[] = [
            'trackEcommerceOrder',
            $order->getId(),
            $order->getTotal(),
            $order->getSubtotal(),
            $order->getTotalTax()
        ];

        $this->render($calls);
    }

    /**
     * @return ItemBuilderInterface
     */
    public function getItemBuilder()
    {
        return $this->itemBuilder;
    }

    /**
     * @param ProductData[] $items
     *
     * @return array
     */
    private function buildItemCalls(array $items): array
    {
        $calls = [];
        foreach ($items as $item) {
            $calls[] = [
                'addEcommerceItem',
                $item->getId(),
                $item->getName(),
                $item->getCategory(),
                $item->getPrice(),
                $item->getQuantity()
            ];
        }

        return $calls;
    }
}
