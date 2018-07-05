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

namespace CoreShop\Bundle\TrackingBundle\Tracker\Google;

use CoreShop\Bundle\TrackingBundle\Model\ProductData;
use CoreShop\Bundle\TrackingBundle\Resolver\ConfigResolver;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Bundle\TrackingBundle\Tracker\AbstractEcommerceTracker;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Pimcore\Analytics\Google\Tracker;
use Pimcore\Analytics\TrackerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UniversalEcommerce extends AbstractEcommerceTracker
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
            'template_prefix' => 'CoreShopTrackingBundle:Tracking/analytics/universal',
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
        if (true === $this->isGlobalSiteTagMode()) {
            return;
        }

        $orderData = $this->itemBuilder->buildOrderAction($order);
        $items = $this->itemBuilder->buildCheckoutItems($order);

        $parameters = [];
        $parameters['order'] = $orderData;
        $parameters['items'] = $items;

        $calls = [
            'ecommerce:addTransaction' => [
                $orderData,
            ],
            'ecommerce:addItem' => [],
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
    protected function isGlobalSiteTagMode()
    {
        $config = $this->config->getGoogleConfig();
        if (false === $config) {
            return false;
        }

        return $config->gtagcode;
    }

    /**
     * Transform product action into universal data object.
     *
     * @param ProductData $item
     *
     * @return array
     */
    protected function transformProductAction(ProductData $item)
    {
        return $this->filterNullValues([
            'sku' => $item->getId(),
            'name' => $item->getName(),
            'category' => $item->getCategory(),
            'price' => round($item->getPrice(), 2),
            'quantity' => $item->getQuantity() ?: 1,
        ]);
    }
}
