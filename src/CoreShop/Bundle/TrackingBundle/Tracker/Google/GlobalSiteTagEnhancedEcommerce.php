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

namespace CoreShop\Bundle\TrackingBundle\Tracker\Google;

use CoreShop\Bundle\TrackingBundle\Model\ActionData;
use CoreShop\Bundle\TrackingBundle\Model\ImpressionData;
use CoreShop\Bundle\TrackingBundle\Model\ProductData;
use CoreShop\Bundle\TrackingBundle\Resolver\ConfigResolver;
use CoreShop\Bundle\TrackingBundle\Tracker\AbstractEcommerceTracker;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Pimcore\Analytics\Google\Tracker as GoogleTracker;
use Pimcore\Analytics\TrackerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GlobalSiteTagEnhancedEcommerce extends AbstractEcommerceTracker
{
    /**
     * @var TrackerInterface
     */
    public $tracker;

    /**
     * @var ConfigResolver
     */
    public $config;

    /**
     * {@inheritdoc}
     */
    public function setTracker(TrackerInterface $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigResolver(ConfigResolver $config)
    {
        $this->config = $config;
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'template_prefix' => 'CoreShopTrackingBundle:Tracking/gtm/classic'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableView(PurchasableInterface $product)
    {
        if ($this->isGoogleTagMode() === false) {
            return;
        }

        $item = $this->itemBuilder->buildPurchasableViewItem($product);

        $parameters = [];
        $actionData = [
            'items' => [$this->transformProductAction($item)]
        ];

        $parameters['actionData'] = $actionData;

        unset($parameters['actionData']['quantity']);

        $result = $this->renderTemplate('product_view', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableImpression(PurchasableInterface $product)
    {
        if ($this->isGoogleTagMode() === false) {
            return;
        }

        $item = $this->itemBuilder->buildPurchasableImpressionItem($product);

        $parameters = [];
        $actionData = [
            'items' => [$this->transformProductImpression($item)]
        ];

        $parameters['actionData'] = $actionData;

        unset($parameters['actionData']['quantity']);

        $result = $this->renderTemplate('product_impression', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartPurchasableAdd(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        if ($this->isGoogleTagMode() === false) {
            return;
        }

        $this->trackPurchasableAction($product, 'add', $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartPurchasableRemove(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        if ($this->isGoogleTagMode() === false) {
            return;
        }

        $this->trackPurchasableAction($product, 'remove', $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep(CartInterface $cart, $stepIdentifier = null, $isFirstStep = false, $checkoutOption = null)
    {
        if ($this->isGoogleTagMode() === false) {
            return;
        }

        $items = $this->itemBuilder->buildCheckoutItemsByCart($cart);
        $cartCoupon = $this->itemBuilder->buildCouponByCart($cart);

        $parameters = [];
        $actionData['items'] = $items;

        if (!is_null($stepIdentifier) || !is_null($checkoutOption)) {
            $actionData['checkout_step'] = $stepIdentifier + 1;
            if (!is_null($checkoutOption)) {
                $actionData['checkout_option'] = $checkoutOption;
            }

            if (!empty($cartCoupon)) {
                $actionData['coupon'] = $cartCoupon;
            }
        }

        $parameters['actionData'] = $actionData;
        $parameters['event'] = $isFirstStep === true ? 'begin_checkout' : 'checkout_progress';

        $result = $this->renderTemplate('checkout', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);

    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutComplete(OrderInterface $order)
    {
        if ($this->isGoogleTagMode() === false) {
            return;
        }

        $orderData = $this->itemBuilder->buildOrderAction($order);
        $items = $this->itemBuilder->buildCheckoutItems($order);

        $actionData = array_merge($this->transformOrder($orderData), ['items' => []]);

        foreach ($items as $item) {
            $actionData['items'][] = $this->transformProductAction($item);
        }

        $parameters['actionData'] = $actionData;

        $result = $this->renderTemplate('checkout_complete', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);

    }

    /**
     * {@inheritdoc}
     */
    protected function trackPurchasableAction(PurchasableInterface $product, $action, $quantity = 1)
    {
        $item = $this->itemBuilder->buildPurchasableActionItem($product);
        $item->setQuantity($quantity);

        $parameters = [];
        $actionData = [];
        $actionData['items'][] = $this->transformProductAction($item);

        $parameters['actionData'] = $actionData;
        $parameters['event'] = $action === 'remove' ? 'remove_from_cart' : 'add_to_cart';

        $result = $this->renderTemplate('product_action', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);

    }

    /**
     * Transform ActionData into gtag data array
     *
     * @param ActionData $actionData
     * @return array
     */
    protected function transformOrder(ActionData $actionData)
    {
        return [
            'transaction_id' => $actionData->getId(),
            'affiliation'    => $actionData->getAffiliation() ?: '',
            'value'          => $actionData->getRevenue(),
            'currency'       => $actionData->getCurrency(),
            'tax'            => $actionData->getTax(),
            'shipping'       => $actionData->getShipping()
        ];
    }

    /**
     * Transform product action into gtag data object
     *
     * @param ProductData $item
     * @return array
     */
    protected function transformProductAction(ProductData $item)
    {
        return $this->filterNullValues([
            'id'            => $item->getId(),
            'name'          => $item->getName(),
            'category'      => $item->getCategory(),
            'brand'         => $item->getBrand(),
            'variant'       => $item->getVariant(),
            'price'         => round($item->getPrice(), 2),
            'quantity'      => $item->getQuantity() ?: 1,
            'list_position' => $item->getPosition()
        ]);
    }

    /**
     * Transform product action into enhanced data object
     *
     * @param ImpressionData $item
     * @return array
     */
    protected function transformProductImpression(ImpressionData $item)
    {
        return $this->filterNullValues([
            'id'       => $item->getId(),
            'name'     => $item->getName(),
            'category' => $item->getCategory(),
            'brand'    => $item->getBrand(),
            'variant'  => $item->getVariant(),
            'price'    => round($item->getPrice(), 2),
            'list'     => $item->getList(),
            'position' => $item->getPosition()
        ]);
    }

    /**
     * @param array $items
     * @return array
     */
    protected function buildCheckoutCalls(array $items)
    {
        $calls = [];
        foreach ($items as $item) {
            $calls[] = $this->transformProductAction($item);
        }

        return $calls;
    }

    /**
     * @return bool
     */
    protected function isGoogleTagMode()
    {
        $config = $this->config->getGoogleConfig();
        if ($config === false) {
            return false;
        }

        return $config->gtagcode;
    }
}
