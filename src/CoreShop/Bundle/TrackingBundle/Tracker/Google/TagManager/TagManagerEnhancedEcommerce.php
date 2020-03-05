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
    public function setTracker(TrackerInterface $tracker): void
    {
        // not implemented in GTM. Use CodeTracker instead.
    }

    /**
     * @param CodeTracker $tracker
     */
    public function setCodeTracker(CodeTracker $tracker): void
    {
        $this->codeTracker = $tracker;
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
            'template_prefix' => '@CoreShopTracking/Tracking/gtm/enhanced',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackProduct($product): void
    {
        $this->ensureDataLayer();

        $parameters = [];
        $actionField = [];
        $actionData = [
            'actionField' => $actionField,
            'products' => [$this->transformProductAction($product)],
        ];

        $parameters['actionData'] = $actionData;

        $result = $this->renderTemplate('product_view', $parameters);
        $this->codeTracker->addCodePart($result);
    }

    /**
     * {@inheritdoc}
     */
    public function trackProductImpression($product): void
    {
        $this->ensureDataLayer();

        $parameters = [];
        $actionData = [
            'impressions' => $this->transformProductAction($product),
            'currencyCode' => $product['currency'],
        ];

        $parameters['actionData'] = $actionData;

        unset($parameters['actionData']['quantity']);

        $result = $this->renderTemplate('product_impression', $parameters);
        $this->codeTracker->addCodePart($result);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartAdd($cart, $product, float $quantity = 1): void
    {
        $this->ensureDataLayer();
        $this->trackCartAction($product, 'add', $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartRemove($cart, $product, float $quantity = 1): void
    {
        $this->ensureDataLayer();
        $this->trackCartAction($product, 'remove', $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep($cart, $stepIdentifier = null, bool $isFirstStep = false, $checkoutOption = null): void
    {
        $this->ensureDataLayer();

        $parameters = [];
        $actionData['products'] = $cart['items'];
        $actionField = [];

        if (null !== $stepIdentifier || null !== $checkoutOption) {
            $actionField['step'] = $stepIdentifier + 1;
            if (null !== $checkoutOption) {
                $actionField['option'] = $checkoutOption;
            }
        }
//
//        if (!empty($cartCoupon)) {
//            $actionData['coupon'] = $cartCoupon;
//        }

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
    public function trackCheckoutComplete($order): void
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
    protected function trackCartAction($product, $action, float $quantity = 1): void
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
     * Transform ActionData into gtag data array.
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
     * Transform product action into gtag data object.
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
            'list_position' => $item['position'],
        ]);
    }

    /**
     * Makes sure data layer is included once before any call.
     */
    protected function ensureDataLayer(): void
    {
        if ($this->dataLayerIncluded) {
            return;
        }

        $result = $this->renderTemplate('data_layer', []);
        $this->codeTracker->addCodePart($result);
        $this->dataLayerIncluded = true;
    }
}
