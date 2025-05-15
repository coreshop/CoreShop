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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Tracking\Tracker;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;

class CompositeTracker implements TrackerInterface
{
    private ?bool $enabled = null;

    public function __construct(
        private TrackingExtractorInterface $extractor,
        private ServiceRegistryInterface $trackerRegistry,
    ) {
    }

    public function isEnabled(): bool
    {
        if ($this->enabled !== null) {
            return $this->enabled;
        }

        foreach ($this->trackerRegistry->all() as $tracker) {
            if ($tracker->isEnabled()) {
                return $this->enabled = true;
            }
        }

        return $this->enabled = false;
    }

    public function setEnabled(bool $enabled): void
    {
    }

    public function trackProduct($product): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $data = $this->extractTrackingData($product);

        $this->compositeTrackerCall('trackProduct', [$data]);
    }

    public function trackProductImpression($product): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $data = $this->extractTrackingData($product);

        $this->compositeTrackerCall('trackProductImpression', [$data]);
    }

    public function trackCartAdd($cart, $product, float $quantity = 1.0): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $cart = $this->extractTrackingData($cart);
        $product = $this->extractTrackingData($product);

        $this->compositeTrackerCall('trackCartAdd', [$cart, $product, $quantity]);
    }

    public function trackCartRemove($cart, $product, float $quantity = 1.0): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $cart = $this->extractTrackingData($cart);
        $product = $this->extractTrackingData($product);

        $this->compositeTrackerCall('trackCartRemove', [$cart, $product, $quantity]);
    }

    public function trackCheckoutStep($cart, $stepIdentifier = null, bool $isFirstStep = false, $checkoutOption = null): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $cart = $this->extractTrackingData($cart);

        $this->compositeTrackerCall('trackCheckoutStep', [$cart, $stepIdentifier, $isFirstStep, $checkoutOption]);
    }

    public function trackCheckoutComplete($order): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $order = $this->extractTrackingData($order);

        $this->compositeTrackerCall('trackCheckoutComplete', [$order]);
    }

    private function compositeTrackerCall(string $function, array $data): void
    {
        /**
         * @var TrackerInterface $tracker
         */
        foreach ($this->trackerRegistry->all() as $tracker) {
            if (!$tracker->isEnabled()) {
                continue;
            }

            call_user_func_array([$tracker, $function], $data);
        }
    }

    private function extractTrackingData($object): array
    {
        return $this->extractor->updateMetadata($object);
    }
}
