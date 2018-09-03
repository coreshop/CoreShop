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

namespace CoreShop\Component\Tracking;

namespace CoreShop\Component\Tracking\Tracker;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;

class CompositeTracker implements TrackerInterface
{
    /**
     * @var TrackingExtractorInterface
     */
    private $extractor;

    /**
     * @var TrackerInterface
     */
    private $trackerRegistry;

    /**
     * @param TrackingExtractorInterface $extractor
     * @param ServiceRegistryInterface $trackerRegistry
     */
    public function __construct(TrackingExtractorInterface $extractor, ServiceRegistryInterface $trackerRegistry)
    {
        $this->extractor = $extractor;
        $this->trackerRegistry = $trackerRegistry;
    }

    public function isEnabled()
    {
        return true;
    }

    public function setEnabled($enabled)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function trackProduct($product)
    {
        $data = $this->extractTrackingData($product);

        $this->compositeTrackerCall('trackProduct', [$data]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackProductImpression($product)
    {
        $data = $this->extractTrackingData($product);

        $this->compositeTrackerCall('trackProductImpression', [$data]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartAdd($cart, $product, $quantity = 1)
    {
        $cart = $this->extractTrackingData($cart);
        $product = $this->extractTrackingData($product);

        $this->compositeTrackerCall('trackCartAdd', [$cart, $product, $quantity]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartRemove($cart, $product, $quantity = 1)
    {
        $cart = $this->extractTrackingData($cart);
        $product = $this->extractTrackingData($product);

        $this->compositeTrackerCall('trackCartRemove', [$cart, $product, $quantity]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep($cart, $stepIdentifier = null, $isFirstStep = false, $checkoutOption = null)
    {
        $cart = $this->extractTrackingData($cart);

        $this->compositeTrackerCall('trackCartRemove', [$cart, $stepIdentifier, $isFirstStep, $checkoutOption]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutComplete($order)
    {
        $order = $this->extractTrackingData($order);

        $this->compositeTrackerCall('trackCheckoutComplete', [$order]);
    }

    /**
     * @param $function
     * @param $data
     */
    private function compositeTrackerCall($function, $data)
    {
         /**
         * @var $tracker TrackerInterface
         */
        foreach ($this->trackerRegistry->all() as $tracker) {
            if (!$tracker->isEnabled()) {
                continue;
            }

            call_user_func_array([$tracker, $function], $data);
        }
    }

    /**
     * @param $object
     * @return array
     */
    private function extractTrackingData($object): array
    {
        return $this->extractor->updateMetadata($object);
    }
}