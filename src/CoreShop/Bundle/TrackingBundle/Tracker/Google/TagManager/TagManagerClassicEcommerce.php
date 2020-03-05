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

class TagManagerClassicEcommerce extends AbstractEcommerceTracker
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
            'template_prefix' => '@CoreShopTracking/Tracking/gtm/classic',
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
    public function trackCartAdd($cart, $product, float $quantity = 1): void
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartRemove($cart, $product, float $quantity = 1): void
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
        $this->ensureDataLayer();

        $items = $order['items'];

        $actionData = array_merge($this->transformOrder($order), ['transactionProducts' => []]);

        foreach ($items as $item) {
            $actionData['transactionProducts'][] = $this->transformProductAction($item);
        }

        $parameters['actionData'] = $actionData;

        $result = $this->renderTemplate('checkout_complete', $parameters);
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
            'transactionId' => $actionData['id'],
            'transactionAffiliation' => $actionData['affiliation'] ?: '',
            'transactionTotal' => $actionData['total'],
            'transactionTax' => $actionData['totalTax'],
            'transactionShipping' => $actionData['shipping'],
            'transactionCurrency' => $actionData['currency'],
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
            'sku' => $item['sku'],
            'name' => $item['name'],
            'category' => $item['category'],
            'price' => round($item['price'], 2),
            'quantity' => $item['quantity'],
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
