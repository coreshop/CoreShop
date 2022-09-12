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

namespace CoreShop\Bundle\ProductBundle\EventListener;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Product\Model\ProductPriceRuleInterface;
use CoreShop\Component\Product\Model\ProductSpecificPriceRuleInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Pimcore\Cache;

final class ClearCachedPriceRulesListener
{
    public function clearCachedRules(ResourceControllerEvent $event): void
    {
        /**
         * @var RuleInterface $rule
         */
        $rule = $event->getSubject();

        if ($rule instanceof ProductPriceRuleInterface || $rule instanceof ProductSpecificPriceRuleInterface) {
            Cache::clearTag('product_price_rule');
        }
    }
}
