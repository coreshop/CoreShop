<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\TrackingBundle\Tracker\Google;

use CoreShop\Bundle\TrackingBundle\Resolver\ConfigResolverInterface;
use CoreShop\Bundle\TrackingBundle\Tracker\AbstractEcommerceTracker;
use Pimcore\Analytics\Google\Tracker as GoogleTracker;
use Pimcore\Analytics\TrackerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GlobalSiteTagEnhancedEcommerce extends AbstractEcommerceTracker
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
            'template_prefix' => '@CoreShopTracking/Tracking/gtag',
        ]);
    }

    public function trackProduct($product): void
    {
        if ($this->isGoogleTagMode() === false) {
            return;
        }

        $parameters = [];
        $actionData = [
            'items' => [$this->transformProductAction($product)],
        ];

        $parameters['actionData'] = $actionData;

        //unset($parameters['actionData']['quantity']);

        $result = $this->renderTemplate('product_view', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);
    }

    public function trackProductImpression($product): void
    {
        if ($this->isGoogleTagMode() === false) {
            return;
        }

        $parameters = [];
        $actionData = [
            'items' => [$this->transformProductAction($product)],
        ];

        $parameters['actionData'] = $actionData;

        //unset($parameters['actionData']['quantity']);

        $result = $this->renderTemplate('product_impression', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);
    }

    public function trackCartAdd($cart, $product, float $quantity = 1.0): void
    {
        if ($this->isGoogleTagMode() === false) {
            return;
        }

        $this->trackCartAction($product, 'add', $quantity);
    }

    public function trackCartRemove($cart, $product, float $quantity = 1.0): void
    {
        if ($this->isGoogleTagMode() === false) {
            return;
        }

        $this->trackCartAction($product, 'remove', $quantity);
    }

    public function trackCheckoutStep($cart, $stepIdentifier = null, bool $isFirstStep = false, $checkoutOption = null): void
    {
        if ($this->isGoogleTagMode() === false) {
            return;
        }

        $parameters = [];
        $parameters['items'] = $cart['items'];
        $actionData = [];

        if (null !== $stepIdentifier || null !== $checkoutOption) {
            $actionData['checkout_step'] = $stepIdentifier + 1;
            if (null !== $checkoutOption) {
                $actionData['checkout_option'] = $checkoutOption;
            }

            if (!empty($cart['voucher'])) {
                $actionData['coupon'] = $cart['voucher'];
            }
        }

        $parameters['actionData'] = $actionData;
        $parameters['event'] = $isFirstStep === true ? 'begin_checkout' : 'checkout_progress';

        $result = $this->renderTemplate('checkout', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);
    }

    public function trackCheckoutComplete($order): void
    {
        if ($this->isGoogleTagMode() === false) {
            return;
        }

        $orderData = $this->transformOrder($order);
        $items = $order['items'];

        $actionData = array_merge($orderData, ['items' => []]);

        foreach ($items as $item) {
            $actionData['items'][] = $this->transformProductAction($item);
        }

        $parameters['actionData'] = $actionData;

        $result = $this->renderTemplate('checkout_complete', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);
    }

    protected function trackCartAction($product, $action, float $quantity = 1.0): void
    {
        $product = $this->transformProductAction($product);
        $product['quantity'] = $quantity;

        $parameters = [];
        $actionData = [];
        $actionData['items'][] = $product;

        $parameters['actionData'] = $actionData;
        $parameters['event'] = $action === 'remove' ? 'remove_from_cart' : 'add_to_cart';

        $result = $this->renderTemplate('product_action', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);
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
            'brand' => $item['brand'],
            'variant' => $item['variant'],
            'price' => round($item['price'], 2),
            'quantity' => $item['quantity'] ?: 1,
            'position' => $item['position'],
            'currency' => $item['currency'],
        ]);
    }

    protected function buildCheckoutCalls(array $items): array
    {
        $calls = [];
        foreach ($items as $item) {
            $calls[] = $this->transformProductAction($item);
        }

        return $calls;
    }

    protected function isGoogleTagMode(): bool
    {
        $config = $this->config->getGoogleConfig();
        if ($config === false) {
            return false;
        }

        return (bool) $config->get('gtagcode');
    }
}
