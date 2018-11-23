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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Webmozart\Assert\Assert;

final class PriceRuleUpdateEventListener
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @param ConfigurationServiceInterface $configurationService
     */
    public function __construct(ConfigurationServiceInterface $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function storeConfigurationThatPriceRulesChanged(ResourceControllerEvent $event)
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
