<?php

namespace CoreShop\Bundle\TrackingBundle\Tracker\Google\TagManager;

use CoreShop\Bundle\TrackingBundle\Resolver\ConfigResolverInterface;
use CoreShop\Bundle\TrackingBundle\Tracker\AbstractEcommerceTracker;
use Pimcore\Analytics\TrackerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagManagerEnhancedEcommerce extends AbstractEcommerceTracker
{
    /**
     * @var CodeTracker
     */
    public $codeTracker;

    /**
     * @var ConfigResolverInterface
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
     * @param ConfigResolverInterface $config
     */
    public function setConfigResolver(ConfigResolverInterface $config)
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
            'template_prefix' => '@CoreShopTracking/Tracking/gtm/enhanced'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackProduct($product)
    {
        $this->ensureDataLayer();

        $parameters = [];
        $actionField = [];
        $actionData = [
            'actionField' => $actionField,
            'products'    => [$this->transformProductAction($product)]
        ];

        $parameters['actionData'] = $actionData;

        $result = $this->renderTemplate('product_view', $parameters);
        $this->codeTracker->addCodePart($result);
    }

    /**
     * {@inheritdoc}
     */
    public function trackProductImpression($product)
    {
        $this->ensureDataLayer();

        $parameters = [];
        $actionData = [
            'impressions'  => $this->transformProductAction($product),
            'currencyCode' => $product['currency']
        ];

        $parameters['actionData'] = $actionData;

        unset($parameters['actionData']['quantity']);

        $result = $this->renderTemplate('product_impression', $parameters);
        $this->codeTracker->addCodePart($result);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartAdd($cart, $product, $quantity = 1)
    {
        $this->ensureDataLayer();
        $this->trackCartAction($product, 'add', $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartRemove($cart, $product, $quantity = 1)
    {
        $this->ensureDataLayer();
        $this->trackCartAction($product, 'remove', $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep($cart, $stepIdentifier = null, $isFirstStep = false, $checkoutOption = null)
    {
        $this->ensureDataLayer();

        $parameters = [];
        $actionData['products'] = $cart['items'];
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
    public function trackCheckoutComplete($order)
    {
        $this->ensureDataLayer();

        $actionData = array_merge(['actionField' => $this->transformOrder($order)], ['products' => []]);

        foreach ($order['items'] as $item) {
            $actionData['products'][] = $item;
        }

        $parameters['actionData'] = $actionData;

        $result = $this->renderTemplate('checkout_complete', $parameters);
        $this->codeTracker->addCodePart($result);

    }

    /**
     * {@inheritdoc}
     */
    protected function trackCartAction($product, $action, $quantity = 1)
    {
        $this->ensureDataLayer();

        $product['quantity'] = 1;

        $parameters = [];
        $actionData = [$action => []];

        if ($action === 'add') {
            $actionData['currencyCode'] = $product['currency'];
        }

        $actionData[$action]['products'][] = $product;

        $parameters['actionData'] = $actionData;
        $parameters['event'] = $action === 'remove' ? 'csRemoveFromCart' : 'csAddToCart';

        $result = $this->renderTemplate('product_action', $parameters);
        $this->codeTracker->addCodePart($result);

    }

    /**
     * Transform ActionData into gtag data array
     *
     * @param array $actionData
     * @return array
     */
    protected function transformOrder($actionData)
    {
        return [
            'id'          => $actionData['id'],
            'affiliation' => $actionData['affiliation'] ?: '',
            'total'       => $actionData['total'],
            'tax'         => $actionData['totalTax'],
            'shipping'    => $actionData['shipping'],
            'currency'    => $actionData['currency']
        ];
    }

    /**
     * Transform product action into gtag data object
     *
     * @param array $item
     * @return array
     */
    protected function transformProductAction($item)
    {
        return $this->filterNullValues([
            'id'            => $item['id'],
            'name'          => $item['name'],
            'category'      => $item['category'],
            'brand'         => $item['brand'],
            'variant'       => $item['variant'],
            'price'         => round($item['price'], 2),
            'quantity'      => $item['quantity'] ?: 1,
            'list_position' => $item['position']
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
