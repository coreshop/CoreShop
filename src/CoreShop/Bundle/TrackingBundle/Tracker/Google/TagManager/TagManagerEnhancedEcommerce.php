<?php

namespace CoreShop\Bundle\TrackingBundle\Tracker\Google\TagManager;

use CoreShop\Bundle\TrackingBundle\Model\ActionData;
use CoreShop\Bundle\TrackingBundle\Model\ImpressionData;
use CoreShop\Bundle\TrackingBundle\Model\ProductData;
use CoreShop\Bundle\TrackingBundle\Resolver\ConfigResolver;
use CoreShop\Bundle\TrackingBundle\Tracker\AbstractEcommerceTracker;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Pimcore\Analytics\TrackerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagManagerEnhancedEcommerce extends AbstractEcommerceTracker
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
            'template_prefix' => 'CoreShopTrackingBundle:Tracking/gtm/enhanced',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableView(PurchasableInterface $product)
    {
        $this->ensureDataLayer();

        $item = $this->itemBuilder->buildPurchasableViewItem($product);

        $parameters = [];
        $actionField = [];
        $actionData = [
            'actionField' => $actionField,
            'products' => [$this->transformProductAction($item)],
        ];

        $parameters['actionData'] = $actionData;

        $result = $this->renderTemplate('product_view', $parameters);
        $this->codeTracker->addCodePart($result);
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableImpression(PurchasableInterface $product)
    {
        $this->ensureDataLayer();

        $item = $this->itemBuilder->buildPurchasableImpressionItem($product);

        $parameters = [];
        $actionData = [
            'impressions' => [$this->transformProductImpression($item)],
            'currencyCode' => $this->getCurrentCurrency(),
        ];

        $parameters['actionData'] = $actionData;

        unset($parameters['actionData']['quantity']);

        $result = $this->renderTemplate('product_impression', $parameters);
        $this->codeTracker->addCodePart($result);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartPurchasableAdd(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        $this->ensureDataLayer();
        $this->trackPurchasableAction($product, 'add', $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartPurchasableRemove(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        $this->ensureDataLayer();
        $this->trackPurchasableAction($product, 'remove', $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep(CartInterface $cart, $stepIdentifier = null, $isFirstStep = false, $checkoutOption = null)
    {
        $this->ensureDataLayer();

        $items = $this->itemBuilder->buildCheckoutItemsByCart($cart);
        $cartCoupon = $this->itemBuilder->buildCouponByCart($cart);

        $parameters = [];
        $actionData['products'] = $items;
        $actionField = [];

        if (!is_null($stepIdentifier) || !is_null($checkoutOption)) {
            $actionField['step'] = $stepIdentifier + 1;
            if (!is_null($checkoutOption)) {
                $actionField['option'] = $checkoutOption;
            }
        }

        if (!empty($cartCoupon)) {
            $actionData['coupon'] = $cartCoupon;
        }

        if (!empty($actionField)) {
            $actionData['actionField'] = $actionField;
        }

        $parameters['actionData'] = $actionData;

        $result = $this->renderTemplate('checkout', $parameters);
        $this->codeTracker->addCodePart($result);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutComplete(OrderInterface $order)
    {
        $this->ensureDataLayer();

        $orderData = $this->itemBuilder->buildOrderAction($order);
        $items = $this->itemBuilder->buildCheckoutItems($order);

        $actionData = array_merge(['actionField' => $this->transformOrder($orderData)], ['products' => []]);

        foreach ($items as $item) {
            $actionData['products'][] = $this->transformProductAction($item);
        }

        $parameters['actionData'] = $actionData;

        $result = $this->renderTemplate('checkout_complete', $parameters);
        $this->codeTracker->addCodePart($result);
    }

    /**
     * {@inheritdoc}
     */
    protected function trackPurchasableAction(PurchasableInterface $product, $action, $quantity = 1)
    {
        $this->ensureDataLayer();

        $item = $this->itemBuilder->buildPurchasableActionItem($product);
        $item->setQuantity($quantity);

        $parameters = [];
        $actionData = [$action => []];

        if ('add' === $action) {
            $actionData['currencyCode'] = $this->getCurrentCurrency();
        }

        $actionData[$action]['products'][] = $this->transformProductAction($item);

        $parameters['actionData'] = $actionData;
        $parameters['event'] = 'remove' === $action ? 'csRemoveFromCart' : 'csAddToCart';

        $result = $this->renderTemplate('product_action', $parameters);
        $this->codeTracker->addCodePart($result);
    }

    /**
     * Transform ActionData into gtag data array.
     *
     * @param ActionData $actionData
     *
     * @return array
     */
    protected function transformOrder(ActionData $actionData)
    {
        return [
            'id' => $actionData->getId(),
            'affiliation' => $actionData->getAffiliation() ?: '',
            'revenue' => $actionData->getRevenue(),
            'currencyCode' => $actionData->getCurrency(),
            'tax' => $actionData->getTax(),
            'shipping' => $actionData->getShipping(),
        ];
    }

    /**
     * Transform product action into gtag data object.
     *
     * @param ProductData $item
     *
     * @return array
     */
    protected function transformProductAction(ProductData $item)
    {
        return $this->filterNullValues([
            'id' => $item->getId(),
            'name' => $item->getName(),
            'category' => $item->getCategory(),
            'brand' => $item->getBrand(),
            'variant' => $item->getVariant(),
            'price' => round($item->getPrice(), 2),
            'quantity' => $item->getQuantity() ?: 1,
            'list_position' => $item->getPosition(),
        ]);
    }

    /**
     * Transform product action into enhanced data object.
     *
     * @param ImpressionData $item
     *
     * @return array
     */
    protected function transformProductImpression(ImpressionData $item)
    {
        return $this->filterNullValues([
            'id' => $item->getId(),
            'name' => $item->getName(),
            'category' => $item->getCategory(),
            'brand' => $item->getBrand(),
            'variant' => $item->getVariant(),
            'price' => round($item->getPrice(), 2),
            'list' => $item->getList(),
            'position' => $item->getPosition(),
        ]);
    }

    /**
     * @param array $items
     *
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
     * Makes sure data layer is included once before any call.
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
