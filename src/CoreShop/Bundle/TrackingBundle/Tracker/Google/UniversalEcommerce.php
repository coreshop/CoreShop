<?php

namespace CoreShop\Bundle\TrackingBundle\Tracker\Google;

use CoreShop\Bundle\TrackingBundle\Model\ProductData;
use CoreShop\Bundle\TrackingBundle\Resolver\ConfigResolver;
use CoreShop\Bundle\TrackingBundle\Tracker\EcommerceTrackerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Bundle\TrackingBundle\Tracker\EcommerceTracker;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Pimcore\Analytics\Google\Tracker;
use Pimcore\Analytics\TrackerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UniversalEcommerce extends EcommerceTracker implements EcommerceTrackerInterface
{
    /**
     * @var TrackerInterface
     */
    public $tracker;

    /**
     * @var ConfigResolver
     */
    public $config;

    /**
     * @param TrackerInterface $tracker
     */
    public function setTracker(TrackerInterface $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * @param ConfigResolver $config
     */
    public function setConfigResolver(ConfigResolver $config)
    {
        $this->config = $config;
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'template_prefix' => 'CoreShopTrackingBundle:Tracking/analytics/universal'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableView(PurchasableInterface $product)
    {
        // not implemented
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
    public function trackCartPurchasableAdd(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartPurchasableRemove(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep(CartInterface $cart, $stepIdentifier = null, $isFirstStep = false, $checkoutOption = null)
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutComplete(OrderInterface $order)
    {
        if ($this->isGoogleTagMode() === true) {
            return;
        }

        $orderData = $this->itemBuilder->buildOrderAction($order);
        $items = $this->itemBuilder->buildCheckoutItems($order);

        $parameters = [];
        $parameters['order'] = $orderData;
        $parameters['items'] = $items;

        $calls = [
            'ecommerce:addTransaction' => [
                $orderData
            ],
            'ecommerce:addItem' => []
        ];

        foreach ($items as $item) {
            $calls['ecommerce:addItem'][] = $this->transformProductAction($item);
        }

        $parameters['calls'] = $calls;

        $result = $this->renderTemplate('checkout_complete', $parameters);
        $this->tracker->addCodePart($result, Tracker::BLOCK_AFTER_TRACK);
    }

    /**
     * @return bool
     */
    protected function isGoogleTagMode()
    {
        $config = $this->config->getGoogleConfig();
        if ($config === false) {
            return false;
        }

        return $config->gtagcode;
    }

    /**
     * Transform product action into universal data object
     *
     * @param ProductData $item
     *
     * @return array
     */
    protected function transformProductAction(ProductData $item)
    {
        return $this->filterNullValues([
            'sku'      => $item->getId(),
            'name'     => $item->getName(),
            'category' => $item->getCategory(),
            'price'    => round($item->getPrice(), 2),
            'quantity' => $item->getQuantity() ?: 1,
        ]);
    }
}
