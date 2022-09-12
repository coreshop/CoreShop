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
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Webmozart\Assert\Assert;

final class PriceRuleUpdateEventListener
{
    public function __construct(private ConfigurationServiceInterface $configurationService)
    {
    }

    public function storeConfigurationThatPriceRulesChanged(ResourceControllerEvent $event): void
    {
        //coreshop.cart_price_rule.post_save
        //coreshop.product_price_rule.post_save
        //coreshop.product_specific_price_rule.post_save

        /**
         * @var RuleInterface $rule
         */
        $rule = $event->getSubject();

        Assert::isInstanceOf($rule, RuleInterface::class);

        $this->configurationService->set('SYSTEM.PRICE_RULE.UPDATE', time());
    }
}
