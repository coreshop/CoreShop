<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\TrackingBundle\Tracker\Google;

use CoreShop\Bundle\TrackingBundle\Resolver\ConfigResolverInterface;
use CoreShop\Bundle\TrackingBundle\Tracker\AbstractEcommerceTracker;
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
     * @var ConfigResolverInterface
     */
    public $config;

    /**
     * @param TrackerInterface $tracker
     */
    public function setTracker(TrackerInterface $tracker): void
    {
        $this->tracker = $tracker;
    }

    /**
     * @param ConfigResolverInterface $config
     */
    public function setConfigResolver(ConfigResolverInterface $config): void
    {
        $this->config = $config;
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'template_prefix' => '@CoreShopTracking/Tracking/analytics/universal',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackProduct($product): void
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function trackProductImpression($product): void
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartAdd($cart, $product, float $quantity = 1.0): void
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartRemove($cart, $product, float $quantity = 1.0): void
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep($cart, $stepIdentifier = null, bool $isFirstStep = false, $checkoutOption = null): void
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * @return bool
     */
    protected function isGlobalSiteTagMode(): bool
    {
        $config = $this->config->getGoogleConfig();
        if ($config === false) {
            return false;
        }

        return (bool) $config->get('gtagcode');
    }

    /**
     * Transform ActionData into classic analytics data array.
     *
     * @param array $actionData
     *
     * @return array
     */
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

    /**
     * Transform product action into enhanced data object.
     *
     * @param array $item
     *
     * @return array
     */
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
}
