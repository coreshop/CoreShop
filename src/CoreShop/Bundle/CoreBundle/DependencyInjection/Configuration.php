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

namespace CoreShop\Bundle\CoreBundle\DependencyInjection;

use CoreShop\Bundle\CoreBundle\Doctrine\ORM\ProductStorePriceRepository;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Core\Model\ProductStorePrice;
use CoreShop\Component\Core\Model\ProductStorePriceInterface;
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
        $rootNode = $treeBuilder->root('coreshop_core');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->scalarNode('send_usage_log')->defaultValue(true)->end()
                ->scalarNode('checkout_manager_factory')->cannotBeEmpty()->end()
                ->scalarNode('after_logout_redirect_route')->defaultValue('coreshop_index')->cannotBeEmpty()->end()
            ->end()
        ;
        $this->addModelsSection($rootNode);
        $this->addPimcoreResourcesSection($rootNode);
        $this->addCheckoutConfigurationSection($rootNode);

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
                        ->arrayNode('product_store_price')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ProductStorePrice::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProductStorePriceInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ProductStorePriceRepository::class)->end()
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
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('provider_item')->defaultValue('/bundles/coreshopcore/pimcore/js/payment/provider/item.js')->end()
                            ->scalarNode('order_detail_carriage')->defaultValue('/bundles/coreshopcore/pimcore/js/order/detail/blocks/carriage.js')->end()
                            ->scalarNode('order_shipment')->defaultValue('/bundles/coreshopcore/pimcore/js/order/shipment.js')->end()
                            ->scalarNode('order_create_step_base')->defaultValue('/bundles/coreshopcore/pimcore/js/sale/create/step/base.js')->end()
                            ->scalarNode('order_create_step_shipping')->defaultValue('/bundles/coreshopcore/pimcore/js/sale/create/step/shipping.js')->end()
                            ->scalarNode('quote_list')->defaultValue('/bundles/coreshopcore/pimcore/js/quote/list.js')->end()
                            ->scalarNode('sale_item')->defaultValue('/bundles/coreshopcore/pimcore/js/store/item.js')->end()
                            ->scalarNode('country_item')->defaultValue('/bundles/coreshopcore/pimcore/js/address/country/item.js')->end()
                            ->scalarNode('report_abstract')->defaultValue('/bundles/coreshopcore/pimcore/js/report/abstract.js')->end()
                            ->scalarNode('report_abstract_store')->defaultValue('/bundles/coreshopcore/pimcore/js/report/abstractStore.js')->end()
                            ->scalarNode('report_monitoring_abstract')->defaultValue('/bundles/coreshopcore/pimcore/js/report/monitoring/abstract.js')->end()
                            ->scalarNode('report_monitoring_disabled')->defaultValue('/bundles/coreshopcore/pimcore/js/report/monitoring/reports/disabledProducts.js')->end()
                            ->scalarNode('report_monitoring_empty_categories')->defaultValue('/bundles/coreshopcore/pimcore/js/report/monitoring/reports/emptyCategories.js')->end()
                            ->scalarNode('report_monitoring_empty_out_of_stock')->defaultValue('/bundles/coreshopcore/pimcore/js/report/monitoring/reports/outOfStockProducts.js')->end()
                            ->scalarNode('report_reports_carrier')->defaultValue('/bundles/coreshopcore/pimcore/js/report/reports/carriers.js')->end()
                            ->scalarNode('report_reports_carts')->defaultValue('/bundles/coreshopcore/pimcore/js/report/reports/carts.js')->end()
                            ->scalarNode('report_reports_carts_abandoned')->defaultValue('/bundles/coreshopcore/pimcore/js/report/reports/carts_abandoned.js')->end()
                            ->scalarNode('report_reports_vouchers')->defaultValue('/bundles/coreshopcore/pimcore/js/report/reports/vouchers.js')->end()
                            ->scalarNode('report_reports_categories')->defaultValue('/bundles/coreshopcore/pimcore/js/report/reports/categories.js')->end()
                            ->scalarNode('report_reports_customers')->defaultValue('/bundles/coreshopcore/pimcore/js/report/reports/customers.js')->end()
                            ->scalarNode('report_reports_payment_providers')->defaultValue('/bundles/coreshopcore/pimcore/js/report/reports/payment_providers.js')->end()
                            ->scalarNode('report_reports_products')->defaultValue('/bundles/coreshopcore/pimcore/js/report/reports/products.js')->end()
                            ->scalarNode('report_reports_sales')->defaultValue('/bundles/coreshopcore/pimcore/js/report/reports/sales.js')->end()
                            ->scalarNode('portlet_orders_carts')->defaultValue('/bundles/coreshopcore/pimcore/js/dashboard/portlets/order_cart.js')->end()
                            ->scalarNode('portlet_sales')->defaultValue('/bundles/coreshopcore/pimcore/js/dashboard/portlets/sales.js')->end()
                            ->scalarNode('carrier_item')->defaultValue('/bundles/coreshopcore/pimcore/js/shipping/carrier/item.js')->end()
                            ->scalarNode('shipping_rule_conditions_categories')->defaultValue('/bundles/coreshopcore/pimcore/js/shipping/rules/conditions/categories.js')->end()
                            ->scalarNode('shipping_rule_conditions_countries')->defaultValue('/bundles/coreshopcore/pimcore/js/shipping/rules/conditions/countries.js')->end()
                            ->scalarNode('shipping_rule_conditions_currencies')->defaultValue('/bundles/coreshopcore/pimcore/js/shipping/rules/conditions/currencies.js')->end()
                            ->scalarNode('shipping_rule_conditions_customerGroups')->defaultValue('/bundles/coreshopcore/pimcore/js/shipping/rules/conditions/customerGroups.js')->end()
                            ->scalarNode('shipping_rule_conditions_customers')->defaultValue('/bundles/coreshopcore/pimcore/js/shipping/rules/conditions/customers.js')->end()
                            ->scalarNode('shipping_rule_conditions_products')->defaultValue('/bundles/coreshopcore/pimcore/js/shipping/rules/conditions/products.js')->end()
                            ->scalarNode('shipping_rule_conditions_stores')->defaultValue('/bundles/coreshopcore/pimcore/js/shipping/rules/conditions/stores.js')->end()
                            ->scalarNode('shipping_rule_conditions_zones')->defaultValue('/bundles/coreshopcore/pimcore/js/shipping/rules/conditions/zones.js')->end()
                            ->scalarNode('product_price_rule_condition_countries')->defaultValue('/bundles/coreshopcore/pimcore/js/product/pricerule/conditions/countries.js')->end()
                            ->scalarNode('product_price_rule_condition_currencies')->defaultValue('/bundles/coreshopcore/pimcore/js/product/pricerule/conditions/currencies.js')->end()
                            ->scalarNode('product_price_rule_condition_customer_groups')->defaultValue('/bundles/coreshopcore/pimcore/js/product/pricerule/conditions/customerGroups.js')->end()
                            ->scalarNode('product_price_rule_condition_customers')->defaultValue('/bundles/coreshopcore/pimcore/js/product/pricerule/conditions/customers.js')->end()
                            ->scalarNode('product_price_rule_condition_quantity')->defaultValue('/bundles/coreshopcore/pimcore/js/product/pricerule/conditions/quantity.js')->end()
                            ->scalarNode('product_price_rule_condition_stores')->defaultValue('/bundles/coreshopcore/pimcore/js/product/pricerule/conditions/stores.js')->end()
                            ->scalarNode('product_price_rule_condition_zones')->defaultValue('/bundles/coreshopcore/pimcore/js/product/pricerule/conditions/zones.js')->end()
                            ->scalarNode('product_price_rule_condition_categories')->defaultValue('/bundles/coreshopcore/pimcore/js/product/pricerule/conditions/categories.js')->end()
                            ->scalarNode('product_specific_price_rule_condition_countries')->defaultValue('/bundles/coreshopcore/pimcore/js/product/specificprice/conditions/countries.js')->end()
                            ->scalarNode('product_specific_price_rule_condition_currencies')->defaultValue('/bundles/coreshopcore/pimcore/js/product/specificprice/conditions/currencies.js')->end()
                            ->scalarNode('product_specific_price_rule_condition_customer_groups')->defaultValue('/bundles/coreshopcore/pimcore/js/product/specificprice/conditions/customerGroups.js')->end()
                            ->scalarNode('product_specific_price_rule_condition_customers')->defaultValue('/bundles/coreshopcore/pimcore/js/product/specificprice/conditions/customers.js')->end()
                            ->scalarNode('product_specific_price_rule_condition_stores')->defaultValue('/bundles/coreshopcore/pimcore/js/product/specificprice/conditions/stores.js')->end()
                            ->scalarNode('product_specific_price_rule_condition_zones')->defaultValue('/bundles/coreshopcore/pimcore/js/product/specificprice/conditions/zones.js')->end()
                            ->scalarNode('cart_pricerule_action_free_shipping')->defaultValue('/bundles/coreshopcore/pimcore/js/cart/pricerules/actions/freeShipping.js')->end()
                            ->scalarNode('cart_pricerule_action_gift_product')->defaultValue('/bundles/coreshopcore/pimcore/js/cart/pricerules/actions/giftProduct.js')->end()
                            ->scalarNode('cart_pricerule_condition_carriers')->defaultValue('/bundles/coreshopcore/pimcore/js/cart/pricerules/conditions/carriers.js')->end()
                            ->scalarNode('cart_pricerule_condition_categories')->defaultValue('/bundles/coreshopcore/pimcore/js/cart/pricerules/conditions/categories.js')->end()
                            ->scalarNode('cart_pricerule_condition_countries')->defaultValue('/bundles/coreshopcore/pimcore/js/cart/pricerules/conditions/countries.js')->end()
                            ->scalarNode('cart_pricerule_condition_currencies')->defaultValue('/bundles/coreshopcore/pimcore/js/cart/pricerules/conditions/currencies.js')->end()
                            ->scalarNode('cart_pricerule_condition_customer_groups')->defaultValue('/bundles/coreshopcore/pimcore/js/cart/pricerules/conditions/customerGroups.js')->end()
                            ->scalarNode('cart_pricerule_condition_customers')->defaultValue('/bundles/coreshopcore/pimcore/js/cart/pricerules/conditions/customers.js')->end()
                            ->scalarNode('cart_pricerule_condition_products')->defaultValue('/bundles/coreshopcore/pimcore/js/cart/pricerules/conditions/products.js')->end()
                            ->scalarNode('cart_pricerule_condition_stores')->defaultValue('/bundles/coreshopcore/pimcore/js/cart/pricerules/conditions/stores.js')->end()
                            ->scalarNode('cart_pricerule_condition_zones')->defaultValue('/bundles/coreshopcore/pimcore/js/cart/pricerules/conditions/zones.js')->end()
                            ->scalarNode('taxrulegroup_item')->defaultValue('/bundles/coreshopcore/pimcore/js/taxation/taxrulegroup/item.js')->end()
                            ->scalarNode('core_extension_data_store_price')->defaultValue('/bundles/coreshopcore/pimcore/js/coreExtension/data/coreShopStorePrice.js')->end()
                            ->scalarNode('core_extension_tag_store_price')->defaultValue('/bundles/coreshopcore/pimcore/js/coreExtension/tags/coreShopStorePrice.js')->end()
                            ->scalarNode('notification_rule_condition_abstract_transition')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/abstractTransition.js')->end()
                            ->scalarNode('notification_rule_action_order_mail')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/actions/orderMail.js')->end()
                            ->scalarNode('notification_rule_condition_messaging_message_type')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/messaging/messageType.js')->end()
                            ->scalarNode('notification_rule_condition_order_carriers')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/order/carriers.js')->end()
                            ->scalarNode('notification_rule_condition_order_comment')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/order/comment.js')->end()
                            ->scalarNode('notification_rule_condition_order_order_state')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/order/orderState.js')->end()
                            ->scalarNode('notification_rule_condition_order_order_state_transition')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/order/orderTransition.js')->end()
                            ->scalarNode('notification_rule_condition_order_payment')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/order/payment.js')->end()
                            ->scalarNode('notification_rule_condition_order_payment_state')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/order/orderPaymentState.js')->end()
                            ->scalarNode('notification_rule_condition_order_payment_transition')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/order/orderPaymentTransition.js')->end()
                            ->scalarNode('notification_rule_condition_order_shipping_state')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/order/orderShippingState.js')->end()
                            ->scalarNode('notification_rule_condition_order_shipping_transition')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/order/orderShippingTransition.js')->end()
                            ->scalarNode('notification_rule_condition_order_invoice_state')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/order/orderInvoiceState.js')->end()
                            ->scalarNode('notification_rule_condition_order_invoice_transition')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/order/orderInvoiceTransition.js')->end()
                            ->scalarNode('notification_rule_condition_payment_payment_state')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/payment/paymentState.js')->end()
                            ->scalarNode('notification_rule_condition_payment_payment_transition')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/payment/paymentTransition.js')->end()
                            ->scalarNode('notification_rule_condition_shipment_state')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/shipment/shipmentState.js')->end()
                            ->scalarNode('notification_rule_condition_shipment_transition')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/shipment/shipmentTransition.js')->end()
                            ->scalarNode('notification_rule_condition_invoice_state')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/invoice/invoiceState.js')->end()
                            ->scalarNode('notification_rule_condition_invoice_transition')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/invoice/invoiceTransition.js')->end()
                            ->scalarNode('notification_rule_condition_user_user_type')->defaultValue('/bundles/coreshopcore/pimcore/js/notification/conditions/user/userType.js')->end()
                            ->scalarNode('object_grid_column_store_price')->defaultValue('/bundles/coreshopcore/pimcore/js/object/gridcolumn/operator/storePrice.js')->end()
                            ->scalarNode('settings')->defaultValue('/bundles/coreshopcore/pimcore/js/settings.js')->end()
                            ->scalarNode('helpers')->defaultValue('/bundles/coreshopcore/pimcore/js/helpers.js')->end()
                            ->scalarNode('resource')->defaultValue('/bundles/coreshopcore/pimcore/js/resource.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('core')->defaultValue('/bundles/coreshopcore/pimcore/css/core.css')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addCheckoutConfigurationSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('checkout')
                ->isRequired()
                ->useAttributeAsKey('name')
                ->requiresAtLeastOneElement()
                ->arrayPrototype()
                    ->children()
                        ->arrayNode('steps')
                        ->useAttributeAsKey('identifier')
                            ->arrayPrototype()
                                ->canBeUnset(true)
                                ->children()
                                    ->scalarNode('step')->isRequired()->end()
                                    ->integerNode('priority')->isRequired()->end()
                                ->end()
                            ->end()
                            ->validate()
                                ->ifTrue(function($array) {
                                    $notValid = false;
                                    foreach ($array as $key => $value) {
                                        if ($key === 'cart') {
                                            $notValid = true;
                                            break;
                                        }
                                    }
                                    return $notValid;
                                })
                                ->thenInvalid('"cart" is a coreshop reserved checkout step. please use another name.')
                            ->end()
                        ->end()

                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
