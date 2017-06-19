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

namespace CoreShop\Bundle\NotificationBundle\DependencyInjection;

use CoreShop\Bundle\NotificationBundle\Controller\NotificationRuleController;
use CoreShop\Bundle\NotificationBundle\Doctrine\ORM\NotificationRuleRepository;
use CoreShop\Bundle\NotificationBundle\Form\Type\NotificationRuleType;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Notification\Model\NotificationRule;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('coreshop_notification');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $this->addModelsSection($rootNode);
        $this->addPimcoreResourcesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addModelsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('notification_rule')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(NotificationRule::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(NotificationRuleInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(NotificationRuleRepository::class)->end()
                                        ->scalarNode('admin_controller')->defaultValue(NotificationRuleController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(NotificationRuleType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addPimcoreResourcesSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('pimcore_admin')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('js')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('resource')->defaultValue('/bundles/coreshopnotification/pimcore/js/resource.js')->end()
                            ->scalarNode('notification_rule_item')->defaultValue('/bundles/coreshopnotification/pimcore/js/rule/item.js')->end()
                            ->scalarNode('notification_rule_panel')->defaultValue('/bundles/coreshopnotification/pimcore/js/rule/panel.js')->end()
                            ->scalarNode('notification_rule_action')->defaultValue('/bundles/coreshopnotification/pimcore/js/rule/action.js')->end()
                            ->scalarNode('notification_rule_condition')->defaultValue('/bundles/coreshopnotification/pimcore/js/rule/condition.js')->end()
                            ->scalarNode('notification_rule_action_mail')->defaultValue('/bundles/coreshopnotification/pimcore/js/rule/actions/mail.js')->end()
                            ->scalarNode('notification_rule_action_order_mail')->defaultValue('/bundles/coreshopnotification/pimcore/js/rule/actions/orderMail.js')->end()
                            ->scalarNode('notification_rule_condition_invoice_invoiceState')->defaultValue('/bundles/coreshopnotification/pimcore/js/rule/conditions/invoice/invoiceState.js')->end()
                            ->scalarNode('notification_rule_condition_messaging_message_type')->defaultValue('/bundles/coreshopnotification/pimcore/js/rule/conditions/messaging/messageType.js')->end()
                            ->scalarNode('notification_rule_condition_order_carriers')->defaultValue('/bundles/coreshopnotification/pimcore/js/rule/conditions/order/carriers.js')->end()
                            ->scalarNode('notification_rule_condition_order_orderState')->defaultValue('/bundles/coreshopnotification/pimcore/js/rule/conditions/order/orderState.js')->end()
                            ->scalarNode('notification_rule_condition_order_payment')->defaultValue('/bundles/coreshopnotification/pimcore/js/rule/conditions/order/payment.js')->end()
                            ->scalarNode('notification_rule_condition_payment_paymentstate')->defaultValue('/bundles/coreshopnotification/pimcore/js/rule/conditions/payment/paymentState.js')->end()
                            ->scalarNode('notification_rule_condition_shipment_shipmentstate')->defaultValue('/bundles/coreshopnotification/pimcore/js/rule/conditions/shipment/shipmentState.js')->end()
                            ->scalarNode('notification_rule_condition_user_usertype')->defaultValue('/bundles/coreshopnotification/pimcore/js/rule/conditions/user/userType.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('notification_rule')->defaultValue('/bundles/coreshopnotification/pimcore/css/notification.css')->end()
                        ->end()
                    ->end()
                    ->scalarNode('permissions')
                        ->cannotBeOverwritten()
                        ->defaultValue(['notification'])
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
