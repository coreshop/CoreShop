<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Tracking\Tracker;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;

class CompositeTracker implements TrackerInterface
{
    public function __construct(private TrackingExtractorInterface $extractor, private ServiceRegistryInterface $trackerRegistry)
    {
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function setEnabled(bool $enabled): void
    {
    }

    public function trackProduct($product): void
    {
        $data = $this->extractTrackingData($product);

        $this->compositeTrackerCall('trackProduct', [$data]);
    }

    public function trackProductImpression($product): void
    {
        $data = $this->extractTrackingData($product);

        $this->compositeTrackerCall('trackProductImpression', [$data]);
    }

    public function trackCartAdd($cart, $product, float $quantity = 1.0): void
    {
        $cart = $this->extractTrackingData($cart);
        $product = $this->extractTrackingData($product);

        $this->compositeTrackerCall('trackCartAdd', [$cart, $product, $quantity]);
    }

    public function trackCartRemove($cart, $product, float $quantity = 1.0): void
    {
        $cart = $this->extractTrackingData($cart);
        $product = $this->extractTrackingData($product);

        $this->compositeTrackerCall('trackCartRemove', [$cart, $product, $quantity]);
    }

    public function trackCheckoutStep($cart, $stepIdentifier = null, bool $isFirstStep = false, $checkoutOption = null): void
    {
        $cart = $this->extractTrackingData($cart);

        $this->compositeTrackerCall('trackCheckoutStep', [$cart, $stepIdentifier, $isFirstStep, $checkoutOption]);
    }

    public function trackCheckoutComplete($order): void
    {
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
