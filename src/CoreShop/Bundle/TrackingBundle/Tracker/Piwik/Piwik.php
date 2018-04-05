<?php

namespace CoreShop\Bundle\TrackingBundle\Tracker\Piwik;

use CoreShop\Bundle\TrackingBundle\Model\ProductData;
use CoreShop\Bundle\TrackingBundle\Tracker\EcommerceTracker;
use CoreShop\Bundle\TrackingBundle\Tracker\EcommerceTrackerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Pimcore\Analytics\TrackerInterface;
use Pimcore\Analytics\Piwik\Tracker as PiwikTracker;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Piwik extends EcommerceTracker implements EcommerceTrackerInterface
{
    /**
     * @var TrackerInterface
     */
    public $tracker;

    /**
     * @var bool
     */
    private $handleCartAdd = true;

    /**
     * @var bool
     */
    private $handleCartRemove = true;

    /**
     * @param TrackerInterface $tracker
     */
    public function setTracker(TrackerInterface $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'template_prefix'    => 'CoreShopTrackingBundle:Tracking/piwik',

            // by default, a cart add/remove delegates to cart update
            // if you manually trigger cart update on every change you can
            // can set this to false to avoid handling of add/remove
            'handle_cart_add'    => true,
            'handle_cart_remove' => true,
        ]);

        $resolver->setAllowedTypes('handle_cart_add', 'bool');
        $resolver->setAllowedTypes('handle_cart_remove', 'bool');
    }

    /**
     * @param array $options
     */
    protected function processOptions(array $options)
    {
        parent::processOptions($options);

        $this->handleCartAdd = $options['handle_cart_add'];
        $this->handleCartRemove = $options['handle_cart_remove'];
    }

    /**
     * @todo: allow multiple categories!
     *
     * {@inheritdoc}
     */
    public function trackPurchasableView(PurchasableInterface $product)
    {
        $item = $this->itemBuilder->buildPurchasableViewItem($product);

        $call = [
            'setEcommerceView',
            $item->getId(),
            $item->getName(),
            $item->getCategory(),
            $item->getPrice()
        ];

        $call[] = $this->filterCategories([$item->getCategory()]);

        $price = $item->getPrice();
        if (!empty($price)) {
            $call[] = $price;
        }

        $result = $this->renderCalls([$call]);
        $this->tracker->addCodePart($result, PiwikTracker::BLOCK_BEFORE_TRACK);

    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableImpression(PurchasableInterface $product)
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartPurchasableActionAdd(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        if ($this->handleCartAdd) {
            $this->trackPurchasableAction($cart, 'add', $quantity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartPurchasableActionRemove(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        if ($this->handleCartRemove) {
            $this->trackPurchasableAction($cart, 'remove', $quantity);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function trackPurchasableAction(CartInterface $cart, $action, $quantity = 1)
    {
        $items = $this->itemBuilder->buildCheckoutItemsByCart($cart);

        $calls = $this->buildItemCalls($items);
        $calls[] = [
            'trackEcommerceCartUpdate',
            $cart->getTotal() / 100
        ];

        $result = $this->renderCalls($calls);
        $this->tracker->addCodePart($result, PiwikTracker::BLOCK_BEFORE_TRACK);

    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep(CartInterface $cart, $stepIdentifier = null, $checkoutOption = null)
    {
        $items = $this->itemBuilder->buildCheckoutItemsByCart($cart);
        $calls = $this->buildItemCalls($items);

        $calls[] = [
            'trackEcommerceCheckout',
            $cart->getId(),
            $cart->getTotal() / 100,
            $cart->getSubtotal() / 100,
            $cart->getTotalTax() / 100
        ];

        $result = $this->renderCalls($calls);
        $this->tracker->addCodePart($result, PiwikTracker::BLOCK_BEFORE_TRACK);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutAction(CartInterface $cart, $stepNumber = null, $checkoutOption = null)
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutComplete(OrderInterface $order)
    {
        $items = $this->itemBuilder->buildCheckoutItems($order);
        $calls = $this->buildItemCalls($items);

        $calls[] = [
            'trackEcommerceOrder',
            $order->getId(),
            $order->getTotal() / 100,
            $order->getSubtotal() / 100,
            $order->getTotalTax() / 100,
            $order->getShipping() / 100,
            $order->getDiscount() / 100
        ];

        $result = $this->renderCalls($calls);
        $this->tracker->addCodePart($result, PiwikTracker::BLOCK_BEFORE_TRACK);
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

    /**
     * @param array $calls
     * @return string
     */
    private function renderCalls(array $calls)
    {
        return $this->renderTemplate('calls', [
            'calls' => $calls
        ]);
    }

    /**
     * @param     $categories
     * @param int $limit
     * @return array|null|string
     */
    private function filterCategories($categories, int $limit = 5)
    {
        if (null === $categories) {
            return $categories;
        }

        $result = null;

        if (is_array($categories)) {
            // add max 5 categories
            $categories = array_slice($categories, 0, 5);

            $result = [];
            foreach ($categories as $category) {
                $category = trim((string)$category);
                if (!empty($category)) {
                    $result[] = $category;
                }
            }

            $result = array_slice($result, 0, $limit);
        } else {
            $result = trim((string)$categories);
        }

        if (!empty($result)) {
            return $result;
        }
    }
}
