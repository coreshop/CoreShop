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

class AnalyticsEnhancedEcommerce extends AbstractEcommerceTracker
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
     * Dependencies to include before any tracking actions
     *
     * @var array
     */
    protected $dependencies = ['ec'];

    /**
     * @var bool
     */
    protected $dependenciesIncluded = false;

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
            'template_prefix' => 'CoreShopTrackingBundle:Tracking/analytics/enhanced'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableView(PurchasableInterface $product)
    {
        if ($this->isGlobalSiteTagMode() === true) {
            return;
        }

        $this->ensureDependencies();

        $item = $this->itemBuilder->buildPurchasableViewItem($product);

        $parameters['productData'] = $this->transformProductAction($item);

        unset($parameters['productData']['price']);
        unset($parameters['productData']['quantity']);

        $result = $this->renderTemplate('product_view', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableImpression(PurchasableInterface $product)
    {
        if ($this->isGlobalSiteTagMode() === true) {
            return;
        }

        $this->ensureDependencies();

        $item = $this->itemBuilder->buildPurchasableImpressionItem($product);

        $parameters = [
            'productData' => $this->transformProductImpression($item)
        ];

        $result = $this->renderTemplate('product_impression', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartPurchasableAdd(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        if ($this->isGlobalSiteTagMode() === true) {
            return;
        }

        $this->ensureDependencies();
        $this->trackPurchasableAction($product, 'add', $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartPurchasableRemove(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        if ($this->isGlobalSiteTagMode() === true) {
            return;
        }

        $this->ensureDependencies();
        $this->trackPurchasableAction($product, 'remove', $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep(CartInterface $cart, $stepIdentifier = null, $isFirstStep = false, $checkoutOption = null)
    {
        if ($this->isGlobalSiteTagMode() === true) {
            return;
        }

        $this->ensureDependencies();

        $items = $this->itemBuilder->buildCheckoutItemsByCart($cart);

        $parameters = [];
        $parameters['items'] = $items;
        $parameters['calls'] = [];

        if (!is_null($stepIdentifier) || !is_null($checkoutOption)) {
            $actionData = ['step' => $stepIdentifier];
            if (!is_null($checkoutOption)) {
                $actionData['option'] = $checkoutOption;
            }

            $parameters['actionData'] = $actionData;
        }

        $result = $this->renderTemplate('checkout', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);

    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutComplete(OrderInterface $order)
    {
        if ($this->isGlobalSiteTagMode() === true) {
            return;
        }

        $this->ensureDependencies();

        $orderData = $this->itemBuilder->buildOrderAction($order);
        $items = $this->itemBuilder->buildCheckoutItems($order);

        $parameters = [];
        $parameters['order'] = $orderData;
        $parameters['items'] = $items;

        $calls = [];
        foreach ($items as $item) {
            $calls[] = $this->transformProductAction($item);
        }

        $parameters['calls'] = $calls;

        $result = $this->renderTemplate('checkout_complete', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);

    }

    /**
     * {@inheritdoc}
     */
    protected function trackPurchasableAction(PurchasableInterface $product, $action, $quantity = 1)
    {
        if ($this->isGlobalSiteTagMode() === true) {
            return;
        }

        $this->ensureDependencies();

        $item = $this->itemBuilder->buildPurchasableActionItem($product);
        $item->setQuantity($quantity);

        $parameters = [];
        $parameters['productData'] = $this->transformProductAction($item);
        $parameters['action'] = $action;

        $result = $this->renderTemplate('product_action', $parameters);
        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);

    }

    /**
     * Transform ActionData into classic analytics data array
     *
     * @param ActionData $actionData
     * @return array
     */
    protected function transformOrder(ActionData $actionData)
    {
        return [
            'id'         => $actionData->getId(),
            'affilation' => $actionData->getAffiliation() ?: '',
            'revenue'    => $actionData->getRevenue(),
            'tax'        => $actionData->getTax(),
            'shipping'   => $actionData->getShipping()
        ];
    }

    /**
     * Transform product action into enhanced data object
     *
     * @param ProductData $item
     * @return array
     */
    protected function transformProductAction(ProductData $item)
    {
        return $this->filterNullValues([
            'id'       => $item->getId(),
            'name'     => $item->getName(),
            'category' => $item->getCategory(),
            'brand'    => $item->getBrand(),
            'variant'  => $item->getVariant(),
            'price'    => round($item->getPrice(), 2),
            'quantity' => $item->getQuantity() ?: 1,
            'position' => $item->getPosition(),
            'coupon'   => $item->getCoupon()
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
     * Makes sure dependencies are included once before any call
     */
    protected function ensureDependencies()
    {
        if ($this->dependenciesIncluded || empty($this->dependencies)) {
            return;
        }

        $result = $this->renderTemplate('dependencies', [
            'dependencies' => $this->dependencies,
            'currency'     => $this->getCurrentCurrency()
        ]);

        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_BEFORE_TRACK);

        $this->dependenciesIncluded = true;
    }

    /**
     * @return bool
     */
    protected function isGlobalSiteTagMode()
    {
        $config = $this->config->getGoogleConfig();
        if ($config === false) {
            return false;
        }

        return $config->gtagcode;
    }
}
