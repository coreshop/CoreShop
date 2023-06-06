<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\TrackingBundle\Tracker\Google;

use CoreShop\Bundle\TrackingBundle\Resolver\ConfigResolverInterface;
use CoreShop\Bundle\TrackingBundle\Tracker\AbstractEcommerceTracker;
use Pimcore\Analytics\Google\Tracker;
use Pimcore\Analytics\TrackerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UniversalEcommerce extends AbstractEcommerceTracker
{
    public TrackerInterface $tracker;

    public ConfigResolverInterface $config;

    public function setTracker(TrackerInterface $tracker): void
    {
        $this->tracker = $tracker;
    }

    public function setConfigResolver(ConfigResolverInterface $config): void
    {
        $this->config = $config;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'template_prefix' => '@CoreShopTracking/Tracking/analytics/universal',
        ]);
    }

    public function trackProduct($product): void
    {
        // not implemented
    }

    public function trackProductImpression($product): void
    {
        // not implemented
    }

    public function trackCartAdd($cart, $product, float $quantity = 1.0): void
    {
        // not implemented
    }

    public function trackCartRemove($cart, $product, float $quantity = 1.0): void
    {
        // not implemented
    }

    public function trackCheckoutStep($cart, $stepIdentifier = null, bool $isFirstStep = false, $checkoutOption = null): void
    {
        // not implemented
    }

    public function trackCheckoutComplete($order): void
    {
        if ($this->isGlobalSiteTagMode() === true) {
            return;
        }

        $orderData = $this->transformOrder($order);
        $items = $order['items'];

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

    protected function isGlobalSiteTagMode(): bool
    {
        $config = $this->config->getGoogleConfig();
        if ($config === null) {
            return false;
        }

        return (bool) $config->get('gtagcode');
    }

    protected function transformOrder(array $actionData): array
    {
        return [
            'id' => $actionData['id'],
            'affiliation' => $actionData['affiliation'] ?: '',
            'total' => $actionData['total'],
            'tax' => $actionData['totalTax'],
            'shipping' => $actionData['shipping'],
            'currency' => $actionData['currency'],
        ];
    }

    protected function transformProductAction(array $item): array
    {
        return $this->filterNullValues([
            'id' => $item['id'],
            'name' => $item['name'],
            'category' => $item['category'],
            'brand' => $item['brand'] ?? null,
            'variant' => $item['variant'] ?? null,
            'price' => round($item['price'], 2),
            'quantity' => $item['quantity'] ?: 1,
            'position' => $item['position'] ?? null,
            'currency' => $item['currency'],
        ]);
    }
}
