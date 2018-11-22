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

namespace CoreShop\Bundle\TrackingBundle\Tracker\Matomo;

use CoreShop\Bundle\TrackingBundle\Tracker\AbstractEcommerceTracker;
use Pimcore\Analytics\TrackerInterface;
use Pimcore\Analytics\Piwik\Tracker as PiwikTracker;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Matomo extends AbstractEcommerceTracker
{
    /**
     * @var TrackerInterface
     */
    public $tracker;

    /**
     * @var bool
     */
    private $handleCartAdd = true;

    /**
     * @var bool
     */
    private $handleCartRemove = true;

    /**
     * @param TrackerInterface $tracker
     */
    public function setTracker(TrackerInterface $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'template_prefix' => '@CoreShopTracking/Tracking/matomo',

            // by default, a cart add/remove delegates to cart update
            // if you manually trigger cart update on every change you can
            // can set this to false to avoid handling of add/remove
            'handle_cart_add' => true,
            'handle_cart_remove' => true,
        ]);

        $resolver->setAllowedTypes('handle_cart_add', 'bool');
        $resolver->setAllowedTypes('handle_cart_remove', 'bool');
    }

    /**
     * @param array $options
     */
    protected function processOptions(array $options)
    {
        parent::processOptions($options);

        $this->handleCartAdd = $options['handle_cart_add'];
        $this->handleCartRemove = $options['handle_cart_remove'];
    }

    /**
     * @todo: allow multiple categories!
     *
     * {@inheritdoc}
     */
    public function trackProduct($product)
    {
        $call = [
            'setEcommerceView',
            $product['id'],
            $product['name'],
            $product['category'],
        ];

        $call[] = $this->filterCategories([$product['categories']]);
        $call[] = $product['price'];

        $result = $this->renderCalls([$call]);
        $this->tracker->addCodePart($result, PiwikTracker::BLOCK_BEFORE_TRACK);
    }

    /**
     * {@inheritdoc}
     */
    public function trackProductImpression($product)
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartAdd($cart, $product, $quantity = 1)
    {
        if ($this->handleCartAdd) {
            $this->trackCartAction($cart, 'add', $quantity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartRemove($cart, $product, $quantity = 1)
    {
        if ($this->handleCartRemove) {
            $this->trackCartAction($cart, 'remove', $quantity);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function trackCartAction($cart, $action, $quantity = 1)
    {
        $calls = $this->buildItemCalls($cart['items']);
        $calls[] = [
            'trackEcommerceCartUpdate',
            $cart['total'],
        ];

        $result = $this->renderCalls($calls);
        $this->tracker->addCodePart($result, PiwikTracker::BLOCK_BEFORE_TRACK);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep($cart, $stepIdentifier = null, $isFirstStep = false, $checkoutOption = null)
    {
        // not implemented (not supported by Matomo)
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutComplete($order)
    {
        $items = $order['items'];

        $calls = $this->buildItemCalls($items);

        $calls[] = [
            'trackEcommerceOrder',
            $order['id'],
            $order['total'],
            $order['subtotal'],
            $order['tax'],
            $order['shipping'],
            $order['discount'],
        ];

        $result = $this->renderCalls($calls);
        $this->tracker->addCodePart($result, PiwikTracker::BLOCK_BEFORE_TRACK);
    }

    /**
     * @param array $items
     *
     * @return array
     */
    private function buildItemCalls(array $items): array
    {
        $calls = [];
        foreach ($items as $item) {
            $calls[] = [
                'addEcommerceItem',
                $item['id'],
                $item['name'],
                $item['category'],
                $item['price'],
                $item['quantity'],
            ];
        }

        return $calls;
    }

    /**
     * @param array $categories
     * @param int   $limit
     *
     * @return array|null|string
     */
    private function filterCategories($categories, int $limit = 5)
    {
        if (null === $categories) {
            return $categories;
        }

        $result = null;

        if (is_array($categories)) {
            // add max 5 categories
            $categories = array_slice($categories, 0, 5);

            $result = [];
            foreach ($categories as $category) {
                $category = trim((string) $category['name']);
                if (!empty($category)) {
                    $result[] = $category;
                }
            }

            return array_slice($result, 0, $limit);
        }

        return [];
    }

    /**
     * @param array $calls
     *
     * @return string
     */
    private function renderCalls(array $calls)
    {
        return $this->renderTemplate('calls', [
            'calls' => $calls,
        ]);
    }
}
