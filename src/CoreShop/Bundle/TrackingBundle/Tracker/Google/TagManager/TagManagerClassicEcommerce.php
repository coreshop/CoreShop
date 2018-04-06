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

namespace CoreShop\Bundle\TrackingBundle\Tracker\Google\TagManager;

use CoreShop\Bundle\TrackingBundle\Model\ActionData;
use CoreShop\Bundle\TrackingBundle\Model\ProductData;
use CoreShop\Bundle\TrackingBundle\Resolver\ConfigResolver;
use CoreShop\Bundle\TrackingBundle\Tracker\AbstractEcommerceTracker;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Pimcore\Analytics\TrackerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagManagerClassicEcommerce extends AbstractEcommerceTracker
{
    /**
     * @var CodeTracker
     */
    public $codeTracker;

    /**
     * @var ConfigResolver
     */
    public $config;

    /**
     * @var bool
     */
    protected $dataLayerIncluded = false;

    /**
     * @param TrackerInterface $tracker
     */
    public function setTracker(TrackerInterface $tracker)
    {
        // not implemented in GTM. Use CodeTracker instead.
    }

    /**
     * @param CodeTracker $tracker
     */
    public function setCodeTracker(CodeTracker $tracker)
    {
        $this->codeTracker = $tracker;
    }

    /**
     * @param ConfigResolver $config
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
        // not implemented
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
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartPurchasableRemove(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep(CartInterface $cart, $stepIdentifier = null, $isFirstStep = false, $checkoutOption = null)
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutComplete(OrderInterface $order)
    {
        $this->ensureDataLayer();

        $orderData = $this->itemBuilder->buildOrderAction($order);
        $items = $this->itemBuilder->buildCheckoutItems($order);

        $actionData = array_merge($this->transformOrder($orderData), ['transactionProducts' => []]);

        foreach ($items as $item) {
            $actionData['transactionProducts'][] = $this->transformProductAction($item);
        }

        $parameters['actionData'] = $actionData;

        $result = $this->renderTemplate('checkout_complete', $parameters);
        $this->codeTracker->addCodePart($result);

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
            'transactionId'          => $actionData->getId(),
            'transactionAffiliation' => $actionData->getAffiliation() ?: '',
            'transactionTotal'       => $actionData->getRevenue(),
            'transactionTax'         => $actionData->getTax(),
            'transactionShipping'    => $actionData->getShipping(),
            'transactionCurrency'    => $actionData->getCurrency()
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
            'id'       => $item->getId(),
            'sku'      => $item->getSku(),
            'name'     => $item->getName(),
            'category' => $item->getCategory(),
            'price'    => round($item->getPrice(), 2),
            'quantity' => $item->getQuantity() ?: 1
        ]);
    }

    /**
     * Makes sure data layer is included once before any call
     */
    protected function ensureDataLayer()
    {
        if ($this->dataLayerIncluded) {
            return;
        }

        $result = $this->renderTemplate('data_layer', []);
        $this->codeTracker->addCodePart($result);
        $this->dataLayerIncluded = true;
    }
}
