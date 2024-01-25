<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Core\Payment\Rule\Condition\ProductsConditionChecker as PaymentRuleProductsConditionChecker;
use CoreShop\Component\Core\Shipping\Rule\Condition\ProductsConditionChecker as ShippingRuleProductsConditionChecker;
use CoreShop\Component\Payment\Model\PaymentProviderRuleInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use Pimcore\Cache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RecursiveVariantCacheInvalidationEventListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'coreshop.product_price_rule.post_save' => ['invalidateRuleCacheAndPrePopulateCache'],
            'coreshop.product_specific_price_rule.post_save' => ['invalidateRuleCacheAndPrePopulateCache'],
            'coreshop.cart_price_rule.post_save' => ['invalidateRuleCacheAndPrePopulateCache'],
            'coreshop.payment_provider_rule.post_save' => ['invalidateRuleCacheAndPrePopulateCache'],
            'coreshop.shipping_rule.post_save' => ['invalidateRuleCacheAndPrePopulateCache'],
        ];
    }

    public function invalidateRuleCacheAndPrePopulateCache(ResourceControllerEvent $event): void
    {
        $resource = $event->getSubject();

        if (!$resource instanceof RuleInterface) {
            return;
        }

        foreach ($resource->getConditions() as $condition) {
            if ($condition->getType() !== 'products') {
                continue;
            }

            $config = $condition->getConfiguration();

            if (!isset($config['include_variants']) || !$config['include_variants']) {
                continue;
            }

            if ($resource instanceof PaymentProviderRuleInterface) {
                Cache::clearTag(PaymentRuleProductsConditionChecker::PAYMENT_PROVIDER_RULE_RECURSIVE_VARIANT_CACHE_TAG);
                continue;
            }

            if ($resource instanceof ShippingRuleInterface) {
                Cache::clearTag(ShippingRuleProductsConditionChecker::SHIPPING_RULE_RECURSIVE_VARIANT_CACHE_TAG);
                continue;
            }

            Cache::clearTag(sprintf('cs_rule_variant_%s', $resource->getId()));
            break;
        }
    }
}
