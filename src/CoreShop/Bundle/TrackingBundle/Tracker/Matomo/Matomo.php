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

use CoreShop\Bundle\TrackingBundle\Model\ProductData;
use CoreShop\Bundle\TrackingBundle\Tracker\AbstractEcommerceTracker;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
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
            'template_prefix'    => 'CoreShopTrackingBundle:Tracking/matomo',

            // by default, a cart add/remove delegates to cart update
            // if you manually trigger cart update on every change you can
            // can set this to false to avoid handling of add/remove
            'handle_cart_add'    => true,
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
    public function trackPurchasableView(PurchasableInterface $product)
    {
        $item = $this->itemBuilder->buildPurchasableViewItem($product);

        $call = [
            'setEcommerceView',
            $item->getId(),
            $item->getName(),
            $item->getCategory(),
            $item->getPrice()
        ];

        $call[] = $this->filterCategories([$item->getCategory()]);

        $price = $item->getPrice();
        if (!empty($price)) {
            $call[] = $price;
        }

        $result = $this->renderCalls([$call]);
        $this->tracker->addCodePart($result, PiwikTracker::BLOCK_BEFORE_TRACK);

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
        if ($this->handleCartAdd) {
            $this->trackPurchasableAction($cart, 'add', $quantity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartPurchasableRemove(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        if ($this->handleCartRemove) {
            $this->trackPurchasableAction($cart, 'remove', $quantity);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function trackPurchasableAction(CartInterface $cart, $action, $quantity = 1)
    {
        $items = $this->itemBuilder->buildCheckoutItemsByCart($cart);

        $calls = $this->buildItemCalls($items);
        $calls[] = [
            'trackEcommerceCartUpdate',
            $cart->getTotal() / 100
        ];

        $result = $this->renderCalls($calls);
        $this->tracker->addCodePart($result, PiwikTracker::BLOCK_BEFORE_TRACK);

    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep(CartInterface $cart, $stepIdentifier = null, $isFirstStep = false, $checkoutOption = null)
    {
        // not implemented (not supported by Matomo)
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutComplete(OrderInterface $order)
    {
        $items = $this->itemBuilder->buildCheckoutItems($order);
        $calls = $this->buildItemCalls($items);

        $calls[] = [
            'trackEcommerceOrder',
            $order->getId(),
            $order->getTotal() / 100,
            $order->getSubtotal() / 100,
            $order->getTotalTax() / 100,
            $order->getShipping() / 100,
            $order->getDiscount() / 100
        ];

        $result = $this->renderCalls($calls);
        $this->tracker->addCodePart($result, PiwikTracker::BLOCK_BEFORE_TRACK);
    }

    /**
     * @param ProductData[] $items
     *
     * @return array
     */
    private function buildItemCalls(array $items): array
    {
        $calls = [];
        foreach ($items as $item) {
            $calls[] = [
                'addEcommerceItem',
                $item->getId(),
                $item->getName(),
                $item->getCategory(),
                $item->getPrice(),
                $item->getQuantity()
            ];
        }

        return $calls;
    }

    /**
     * @param array $calls
     * @return string
     */
    private function renderCalls(array $calls)
    {
        return $this->renderTemplate('calls', [
            'calls' => $calls
        ]);
    }

    /**
     * @param     $categories
     * @param int $limit
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
                $category = trim((string)$category);
                if (!empty($category)) {
                    $result[] = $category;
                }
            }

            $result = array_slice($result, 0, $limit);
        } else {
            $result = trim((string)$categories);
        }

        if (!empty($result)) {
            return $result;
        }
    }
}
