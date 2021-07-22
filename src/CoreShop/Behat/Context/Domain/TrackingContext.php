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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Behat\Service\Tracking\ConfigResolver;
use CoreShop\Bundle\TrackingBundle\Tracker\Google\AnalyticsEnhancedEcommerce;
use CoreShop\Bundle\TrackingBundle\Tracker\Google\GlobalSiteTagEnhancedEcommerce;
use CoreShop\Bundle\TrackingBundle\Tracker\Google\TagManager\TagManagerClassicEcommerce;
use CoreShop\Bundle\TrackingBundle\Tracker\Google\TagManager\TagManagerEnhancedEcommerce;
use CoreShop\Bundle\TrackingBundle\Tracker\Google\UniversalEcommerce;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Registry\ServiceRegistry;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;
use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Pimcore\Analytics\AbstractTracker;
use Pimcore\Analytics\Code\CodeCollector;
use Pimcore\Analytics\Google\Tracker;
use Webmozart\Assert\Assert;

final class TrackingContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private TrackingExtractorInterface $trackingExtractor;
    private ServiceRegistry $trackerRegistry;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        TrackingExtractorInterface $trackingExtractor,
        ServiceRegistry $trackerRegistry
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->trackingExtractor = $trackingExtractor;
        $this->trackerRegistry = $trackerRegistry;

        /**
         * @var AnalyticsEnhancedEcommerce $googleAnalyticsEnhancedTracker
         */
        $googleAnalyticsEnhancedTracker = $this->trackerRegistry->get('google-analytics-enhanced-ecommerce');
        /**
         * @var GlobalSiteTagEnhancedEcommerce $googleGTagEnhancedTracker
         */
        $googleGTagEnhancedTracker = $this->trackerRegistry->get('google-gtag-enhanced-ecommerce');
        /**
         * @var UniversalEcommerce $googleAnalyticsUniversalTracker
         */
        $googleAnalyticsUniversalTracker = $this->trackerRegistry->get('google-analytics-universal-ecommerce');

        $googleAnalyticsEnhancedTracker->setConfigResolver(new ConfigResolver());
        $googleGTagEnhancedTracker->setConfigResolver(new ConfigResolver());
        $googleAnalyticsUniversalTracker->setConfigResolver(new ConfigResolver());
    }

    /**
     * @Then /^tracking (product) impression with tracker "([^"]+)" should generate:$/
     */
    public function trackProductImpression(ProductInterface $product, $tracker, PyStringNode $code): void
    {
        $tracker = $this->getTracker($tracker);

        $tracker->trackProductImpression($this->trackingExtractor->updateMetadata($product));

        $result = str_replace('##id##', (string)$product->getId(), $code->getRaw());

        Assert::eq(preg_replace("/\r|\n/", '', $this->getRenderedPartForTracker($tracker)), preg_replace("/\r|\n/", '', $result));
    }

    /**
     * @Then /^tracking (product) with tracker "([^"]+)" should generate:$/
     */
    public function trackProductView(ProductInterface $product, $tracker, PyStringNode $code): void
    {
        $tracker = $this->getTracker($tracker);

        $tracker->trackProduct($this->trackingExtractor->updateMetadata($product));

        $result = str_replace('##id##', (string)$product->getId(), $code->getRaw());

        Assert::eq($this->getRenderedPartForTracker($tracker), $result);
    }

    /**
     * @Then /^tracking cart-add for (my cart) with (product) with tracker "([^"]+)" should generate:$/
     */
    public function trackCartAdd(OrderInterface $cart, ProductInterface $product, $tracker, PyStringNode $code): void
    {
        $tracker = $this->getTracker($tracker);

        $tracker->trackCartAdd($this->trackingExtractor->updateMetadata($cart), $this->trackingExtractor->updateMetadata($product), 1);

        $params = ['cart' => $cart, 'product' => $product];

        $result = str_replace('##id##', (string)$product->getId(), $code->getRaw());

        if (count($cart->getItems()) > 0) {
            $result = str_replace('##item_id##', (string)$cart->getItems()[0]->getId(), $result);
        }

        Assert::eq($this->getRenderedPartForTracker($tracker), $result);
    }

    /**
     * @Then /^tracking cart-remove for (my cart) with (product) with tracker "([^"]+)" should generate:$/
     */
    public function trackCartRemove(OrderInterface $cart, ProductInterface $product, $tracker, PyStringNode $code): void
    {
        $tracker = $this->getTracker($tracker);

        $tracker->trackCartRemove($this->trackingExtractor->updateMetadata($cart), $this->trackingExtractor->updateMetadata($product), 1);

        $result = str_replace('##id##', (string)$product->getId(), $code->getRaw());

        Assert::eq($this->getRenderedPartForTracker($tracker), $result);
    }

    /**
     * @Then /^tracking checkout step for (my cart) with tracker "([^"]+)" should generate:$/
     */
    public function trackCheckoutStep(OrderInterface $cart, $tracker, PyStringNode $code): void
    {
        $tracker = $this->getTracker($tracker);

        $tracker->trackCheckoutStep($this->trackingExtractor->updateMetadata($cart));

        $result = str_replace('##id##', (string)$cart->getId(), $code->getRaw());
        $result = str_replace('##item_id##', (string)$cart->getItems()[0]->getId(), $result);

        Assert::eq($this->getRenderedPartForTracker($tracker), $result);
    }

    /**
     * @Then /^tracking (my order) checkout complete with tracker "([^"]+)" should generate:$/
     */
    public function trackCheckoutComplete(OrderInterface $order, $tracker, PyStringNode $code): void
    {
        $tracker = $this->getTracker($tracker);

        $tracker->trackCheckoutComplete($this->trackingExtractor->updateMetadata($order));

        $result = str_replace('##id##', (string)$order->getId(), $code->getRaw());
        $result = str_replace('##item_id##', (string)$order->getItems()[0]->getId(), $result);

        Assert::eq($this->getRenderedPartForTracker($tracker), $code);
    }

    private function getRenderedPartForTracker(TrackerInterface $tracker): string
    {
        $code = '';

        if ($tracker instanceof TagManagerEnhancedEcommerce) {
            $code = implode('', $tracker->getCodeTracker()->getBlocks());
        } elseif ($tracker instanceof TagManagerClassicEcommerce) {
            $code = implode('', $tracker->getCodeTracker()->getBlocks());
        } elseif ($tracker instanceof AnalyticsEnhancedEcommerce ||
            $tracker instanceof GlobalSiteTagEnhancedEcommerce ||
            $tracker instanceof UniversalEcommerce
        ) {
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

            if ($tracker instanceof UniversalEcommerce) {
                $code = implode(
                    PHP_EOL,
                    $blocks[CodeCollector::CONFIG_KEY_GLOBAL][Tracker::BLOCK_AFTER_TRACK]['append']
                );
            } else {
                $code = implode(
                    PHP_EOL,
                    $blocks[CodeCollector::CONFIG_KEY_GLOBAL][Tracker::BLOCK_BEFORE_TRACK]['append']
                );
            }

            return trim(preg_replace('/\s+/', ' ', $code));
        }

        return trim($code);
    }

    private function getTracker(string $trackerIdentifier): TrackerInterface
    {
        $tracker = $this->trackerRegistry->get($trackerIdentifier);

        /**
         * @var $tracker TrackerInterface
         */
        Assert::isInstanceOf($tracker, TrackerInterface::class);

        return $tracker;
    }
}
