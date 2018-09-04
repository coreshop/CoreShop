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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Behat\Service\Tracking\ConfigResolver;
use CoreShop\Bundle\TrackingBundle\Tracker\Google\AnalyticsEnhancedEcommerce;
use CoreShop\Bundle\TrackingBundle\Tracker\Google\TagManager\TagManagerClassicEcommerce;
use CoreShop\Bundle\TrackingBundle\Tracker\Google\TagManager\TagManagerEnhancedEcommerce;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Registry\ServiceRegistry;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;
use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Pimcore\Analytics\AbstractTracker;
use Pimcore\Analytics\Code\CodeCollector;
use Pimcore\Analytics\Google\Tracker;
use Pimcore\Analytics\SiteId\SiteId;
use Webmozart\Assert\Assert;

final class TrackingContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var TrackingExtractorInterface
     */
    private $trackingExtractor;

    /**
     * @var ServiceRegistry
     */
    private $trackerRegistry;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param TrackingExtractorInterface $trackingExtractor
     * @param ServiceRegistry $trackerRegistry
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        TrackingExtractorInterface $trackingExtractor,
        ServiceRegistry $trackerRegistry
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->trackingExtractor = $trackingExtractor;
        $this->trackerRegistry = $trackerRegistry;

        $this->trackerRegistry->get('google-analytics-enhanced-ecommerce')->setConfigResolver(new ConfigResolver());
    }

    /**
     * @Then /^tracking (product) impression with tracker "([^"]+)" should generate:$/
     */
    public function trackProductImpression(ProductInterface $product, $tracker, PyStringNode $code)
    {
        $tracker = $this->getTracker($tracker);

        $tracker->trackProductImpression($this->trackingExtractor->updateMetadata($product));

        $placeholderHelper = new \Pimcore\Placeholder();
        $code = $placeholderHelper->replacePlaceholders($code->getRaw(), ['product' => $product]);

        Assert::eq(preg_replace( "/\r|\n/", "", $this->getRenderedPartForTracker($tracker)), preg_replace( "/\r|\n/", "", $code));
    }

    /**
     * @Then /^tracking (product) with tracker "([^"]+)" should generate:$/
     */
    public function trackProductView(ProductInterface $product, $tracker, PyStringNode $code)
    {
        $tracker = $this->getTracker($tracker);

        $tracker->trackProduct($this->trackingExtractor->updateMetadata($product));

        $placeholderHelper = new \Pimcore\Placeholder();
        $code = $placeholderHelper->replacePlaceholders($code->getRaw(), ['product' => $product]);

        Assert::eq($this->getRenderedPartForTracker($tracker), $code);
    }

    /**
     * @Then /^tracking cart-add for (my cart) with (product) with tracker "([^"]+)" should generate:$/
     */
    public function trackCartAdd(CartInterface $cart, ProductInterface $product, $tracker, PyStringNode $code)
    {
        $tracker = $this->getTracker($tracker);

        $tracker->trackCartAdd($this->trackingExtractor->updateMetadata($cart), $this->trackingExtractor->updateMetadata($product), 1);

        $placeholderHelper = new \Pimcore\Placeholder();
        $code = $placeholderHelper->replacePlaceholders($code->getRaw(), ['cart' => $cart, 'product' => $product]);

        Assert::eq($this->getRenderedPartForTracker($tracker), $code);
    }

    /**
     * @Then /^tracking cart-remove for (my cart) with (product) with tracker "([^"]+)" should generate:$/
     */
    public function trackCartRemove(CartInterface $cart, ProductInterface $product, $tracker, PyStringNode $code)
    {
        $tracker = $this->getTracker($tracker);

        $tracker->trackCartRemove($this->trackingExtractor->updateMetadata($cart), $this->trackingExtractor->updateMetadata($product), 1);

        $placeholderHelper = new \Pimcore\Placeholder();
        $code = $placeholderHelper->replacePlaceholders($code->getRaw(), ['cart' => $cart, 'product' => $product]);

        Assert::eq($this->getRenderedPartForTracker($tracker), $code);
    }

    /**
     * @Then /^tracking checkout step for (my cart) with tracker "([^"]+)" should generate:$/
     */
    public function trackCheckoutStep(CartInterface $cart, $tracker, PyStringNode $code)
    {
        $tracker = $this->getTracker($tracker);

        $tracker->trackCheckoutStep($this->trackingExtractor->updateMetadata($cart));

        $placeholderHelper = new \Pimcore\Placeholder();
        $code = $placeholderHelper->replacePlaceholders($code->getRaw(), ['cart' => $cart, 'cartItem' => $cart->getItems()[0]->getId()]);

        Assert::eq($this->getRenderedPartForTracker($tracker), $code);
    }

    /**
     * @Then /^tracking (my order) checkout complete with tracker "([^"]+)" should generate:$/
     */
    public function trackCheckoutComplete(OrderInterface $order, $tracker, PyStringNode $code)
    {
        $tracker = $this->getTracker($tracker);

        $tracker->trackCheckoutComplete($this->trackingExtractor->updateMetadata($order));

        $placeholderHelper = new \Pimcore\Placeholder();
        $code = $placeholderHelper->replacePlaceholders($code->getRaw(), [
            'order' => $order,
            'orderItem' => $order->getItems()[0],
        ]);

        Assert::eq($this->getRenderedPartForTracker($tracker), $code);
    }

    /**
     * @param TrackerInterface $tracker
     * @return null|string
     */
    private function getRenderedPartForTracker(TrackerInterface $tracker)
    {
        $code = '';

        if ($tracker instanceof TagManagerEnhancedEcommerce) {
            $code = implode('', $tracker->codeTracker->getBlocks());
        }
        else if ($tracker instanceof TagManagerClassicEcommerce) {
            $code = implode('', $tracker->codeTracker->getBlocks());
        }
        else if ($tracker instanceof AnalyticsEnhancedEcommerce) {
            $trackerReflector = new \ReflectionClass(AbstractTracker::class);
            $codeCollectorProperty = $trackerReflector->getProperty('codeCollector');
            $codeCollectorProperty->setAccessible(true);

            $codeCollector = $codeCollectorProperty->getValue($tracker->tracker);

            $codeCollectorProperty->setAccessible(false);

            $codeCollectorReflector = new \ReflectionClass(CodeCollector::class);

            $codePartsProperty = $codeCollectorReflector->getProperty('codeParts');
            $codePartsProperty->setAccessible(true);

            $blocks = $codePartsProperty->getValue($codeCollector);

            $codePartsProperty->setAccessible(false);

            return trim(preg_replace('/\s+/', ' ', implode(PHP_EOL, $blocks[CodeCollector::CONFIG_KEY_GLOBAL][Tracker::BLOCK_BEFORE_TRACK]['append'])));
        }

        return trim($code);
    }

    /**
     * @param $tracker
     * @return TrackerInterface
     */
    private function getTracker($tracker)
    {
        $tracker = $this->trackerRegistry->get($tracker);

        /**
         * @var $tracker TrackerInterface
         */
        Assert::isInstanceOf($tracker, TrackerInterface::class);

        return $tracker;
    }
}
