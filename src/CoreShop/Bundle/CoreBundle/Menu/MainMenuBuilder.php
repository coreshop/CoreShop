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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Menu;

use CoreShop\Bundle\MenuBundle\Builder\MenuBuilderInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MainMenuBuilder implements MenuBuilderInterface
{
    public function buildMenu(ItemInterface $menuItem, FactoryInterface $factory, string $type): void
    {
        $menuItem->setLabel('coreshop');
        $menuItem->setAttributes([
            'class' => 'coreshop_logo_menu',
            'content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 76 87">
                    <defs>
                        <style>.cls-1{fill:#cd1017;fill-rule:evenodd;}</style>
                    </defs>
                    <path class="cls-1"
                          d="M48.57,50.58,38,56.69,14.31,43V29.82L38,16.15l31.87,18.4V25.1L38,6.7,6.13,25.1V47.73L38,66.13,56.75,55.3V45.86l-8.18-4.72v9.44ZM27.43,36.42,38,30.31,61.69,44V57.18L38,70.85,6.13,52.45V61.9L38,80.3,69.87,61.9V39.27L38,20.87,19.25,31.7v9.44l8.18,4.72V36.42Z"/>
                </svg>
            ',
        ]);

        $menuItem->addChild('coreshop_order_by_number')
            ->setLabel('coreshop_order_by_number')
            ->setAttribute('permission', 'coreshop_permission_order_detail')
            ->setAttribute('iconCls', 'coreshop_nav_icon_order')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'open_order_by_number')
            ->setExtra('order', 10);

        $menuItem->addChild('coreshop_quote_by_number')
            ->setLabel('coreshop_quote_by_number')
            ->setAttribute('permission', 'coreshop_permission_quote_detail')
            ->setAttribute('iconCls', 'coreshop_nav_icon_quote')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'coreshop_quote_by_number')
            ->setExtra('order', 20);

        $menuItem->addChild('coreshop_settings')
            ->setLabel('coreshop_settings')
            ->setAttribute('permission', 'coreshop_permission_settings')
            ->setAttribute('iconCls', 'coreshop_nav_icon_settings')
            ->setAttribute('resource', 'coreshop.core')
            ->setAttribute('function', 'settings')
            ->setExtra('order', 30);

        $priceRules = $menuItem
            ->addChild('coreshop_pricerules')
            ->setLabel('coreshop_pricerules')
            ->setAttribute('iconCls', 'coreshop_nav_icon_price_rule')
            ->setAttribute('container', true)
            ->setExtra('order', 40);

        $priceRules
            ->addChild('coreshop_cart_pricerules')
            ->setLabel('coreshop_cart_pricerules')
            ->setAttribute('permission', 'coreshop_permission_cart_price_rule')
            ->setAttribute('iconCls', 'coreshop_nav_icon_price_rule')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'cart_price_rule')
            ->setExtra('order', 10);

        $priceRules
            ->addChild('coreshop_product_pricerules')
            ->setLabel('coreshop_product_pricerules')
            ->setAttribute('permission', 'coreshop_permission_product_price_rule')
            ->setAttribute('iconCls', 'coreshop_nav_icon_price_rule')
            ->setAttribute('resource', 'coreshop.product')
            ->setAttribute('function', 'product_price_rule')
            ->setExtra('order', 20);

        $localization = $menuItem
            ->addChild('coreshop_localization')
            ->setLabel('coreshop_localization')
            ->setAttribute('iconCls', 'coreshop_nav_icon_localization')
            ->setAttribute('container', true)
            ->setExtra('order', 50);

        $localization
            ->addChild('coreshop_countries')
            ->setLabel('coreshop_countries')
            ->setAttribute('permission', 'coreshop_permission_country')
            ->setAttribute('iconCls', 'coreshop_nav_icon_country')
            ->setAttribute('resource', 'coreshop.address')
            ->setAttribute('function', 'country')
            ->setExtra('order', 10);

        $localization
            ->addChild('coreshop_states')
            ->setLabel('coreshop_states')
            ->setAttribute('permission', 'coreshop_permission_state')
            ->setAttribute('iconCls', 'coreshop_nav_icon_state')
            ->setAttribute('resource', 'coreshop.address')
            ->setAttribute('function', 'state')
            ->setExtra('order', 20);

        $localization
            ->addChild('coreshop_currencies')
            ->setLabel('coreshop_currencies')
            ->setAttribute('permission', 'coreshop_permission_currency')
            ->setAttribute('iconCls', 'coreshop_nav_icon_currency')
            ->setAttribute('resource', 'coreshop.currency')
            ->setAttribute('function', 'currency')
            ->setExtra('order', 30);

        $localization
            ->addChild('coreshop_exchange_rates')
            ->setLabel('coreshop_exchange_rates')
            ->setAttribute('permission', 'coreshop_permission_exchange_rate')
            ->setAttribute('iconCls', 'coreshop_nav_icon_exchange_rate')
            ->setAttribute('resource', 'coreshop.currency')
            ->setAttribute('function', 'exchange_rate')
            ->setExtra('order', 40);

        $localization
            ->addChild('coreshop_zones')
            ->setLabel('coreshop_zones')
            ->setAttribute('permission', 'coreshop_permission_zone')
            ->setAttribute('iconCls', 'coreshop_nav_icon_zone')
            ->setAttribute('resource', 'coreshop.address')
            ->setAttribute('function', 'zone')
            ->setExtra('order', 50);

        $localization
            ->addChild('coreshop_taxes')
            ->setLabel('coreshop_taxes')
            ->setAttribute('permission', 'coreshop_permission_tax_item')
            ->setAttribute('iconCls', 'coreshop_nav_icon_taxes')
            ->setAttribute('resource', 'coreshop.taxation')
            ->setAttribute('function', 'tax_item')
            ->setExtra('order', 60);

        $localization
            ->addChild('coreshop_taxrulegroups')
            ->setLabel('coreshop_taxrulegroups')
            ->setAttribute('permission', 'coreshop_permission_tax_rule_group')
            ->setAttribute('iconCls', 'coreshop_nav_icon_tax_rule_groups')
            ->setAttribute('resource', 'coreshop.taxation')
            ->setAttribute('function', 'tax_rule_group')
            ->setExtra('order', 70);

        $ordersMenu = $menuItem
            ->addChild('coreshop_order')
            ->setLabel('coreshop_order')
            ->setAttribute('iconCls', 'coreshop_nav_icon_order')
            ->setAttribute('container', true)
            ->setExtra('order', 60);

        $ordersMenu
            ->addChild('coreshop_orders')
            ->setLabel('coreshop_orders')
            ->setAttribute('permission', 'coreshop_permission_order_list')
            ->setAttribute('iconCls', 'coreshop_nav_icon_orders')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'orders')
            ->setExtra('order', 10);

        $ordersMenu
            ->addChild('coreshop_order_create')
            ->setLabel('coreshop_order_create')
            ->setAttribute('permission', 'coreshop_permission_order_create')
            ->setAttribute('iconCls', 'coreshop_nav_icon_order_create')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'create_order')
            ->setExtra('order', 20);

        $ordersMenu
            ->addChild('coreshop_quotes')
            ->setLabel('coreshop_quotes')
            ->setAttribute('permission', 'coreshop_permission_quote_list')
            ->setAttribute('iconCls', 'coreshop_nav_icon_quotes')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'quotes')
            ->setExtra('order', 30);

        $ordersMenu
            ->addChild('coreshop_quote_create')
            ->setLabel('coreshop_quote_create')
            ->setAttribute('permission', 'coreshop_permission_quote_create')
            ->setAttribute('iconCls', 'coreshop_nav_icon_quote_create')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'create_quote')
            ->setExtra('order', 40);

        $ordersMenu
            ->addChild('coreshop_carts')
            ->setLabel('coreshop_carts')
            ->setAttribute('permission', 'coreshop_permission_cart_list')
            ->setAttribute('iconCls', 'coreshop_nav_icon_carts')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'carts')
            ->setExtra('order', 50);

        $ordersMenu
            ->addChild('coreshop_cart_create')
            ->setLabel('coreshop_cart_create')
            ->setAttribute('permission', 'coreshop_permission_cart_create')
            ->setAttribute('iconCls', 'coreshop_nav_icon_cart_create')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'create_cart')
            ->setExtra('order', 60);

        $carriersMenu = $menuItem
            ->addChild('coreshop_shipping')
            ->setLabel('coreshop_shipping')
            ->setAttribute('iconCls', 'coreshop_nav_icon_shipping')
            ->setAttribute('container', true)
            ->setExtra('order', 70);

        $carriersMenu
            ->addChild('coreshop_carriers')
            ->setLabel('coreshop_carriers')
            ->setAttribute('permission', 'coreshop_permission_carrier')
            ->setAttribute('iconCls', 'coreshop_nav_icon_carriers')
            ->setAttribute('resource', 'coreshop.shipping')
            ->setAttribute('function', 'carrier')
            ->setExtra('order', 10);

        $carriersMenu
            ->addChild('coreshop_carriers_shipping_rules')
            ->setLabel('coreshop_carriers_shipping_rules')
            ->setAttribute('permission', 'coreshop_permission_shipping_rule')
            ->setAttribute('iconCls', 'coreshop_nav_icon_carrier_shipping_rule')
            ->setAttribute('resource', 'coreshop.shipping')
            ->setAttribute('function', 'shipping_rules')
            ->setExtra('order', 20);

        $productsMenu = $menuItem
            ->addChild('coreshop_product')
            ->setLabel('coreshop_product')
            ->setAttribute('iconCls', 'coreshop_nav_icon_product')
            ->setAttribute('container', true)
            ->setExtra('order', 80);

        $productsMenu
            ->addChild('coreshop_indexes')
            ->setLabel('coreshop_indexes')
            ->setAttribute('permission', 'coreshop_permission_index')
            ->setAttribute('iconCls', 'coreshop_nav_icon_indexes')
            ->setAttribute('resource', 'coreshop.index')
            ->setAttribute('function', 'index')
            ->setExtra('order', 10);

        $productsMenu
            ->addChild('coreshop_product_units')
            ->setLabel('coreshop_product_units')
            ->setAttribute('permission', 'coreshop_product_unit')
            ->setAttribute('iconCls', 'coreshop_nav_icon_product_units')
            ->setAttribute('resource', 'coreshop.product')
            ->setAttribute('function', 'product_unit')
            ->setExtra('order', 30);

        $productsMenu
            ->addChild('coreshop_filters')
            ->setLabel('coreshop_filters')
            ->setAttribute('permission', 'coreshop_permission_filter')
            ->setAttribute('iconCls', 'coreshop_nav_icon_filters')
            ->setAttribute('resource', 'coreshop.index')
            ->setAttribute('function', 'filter')
            ->setExtra('order', 20);

        $customersMenu = $menuItem
            ->addChild('coreshop_customer')
            ->setLabel('coreshop_customer')
            ->setAttribute('iconCls', 'coreshop_nav_icon_customer')
            ->setAttribute('container', true)
            ->setExtra('order', 81);

        $customersMenu
            ->addChild('coreshop_customers')
            ->setLabel('coreshop_customers')
            ->setAttribute('permission', 'coreshop_permission_customer_list')
            ->setAttribute('iconCls', 'coreshop_nav_icon_customers')
            ->setAttribute('resource', 'coreshop.customer')
            ->setAttribute('function', 'customers')
            ->setExtra('order', 10);

        $customersMenu
            ->addChild('coreshop_customer_groups')
            ->setLabel('coreshop_customer_groups')
            ->setAttribute('permission', 'coreshop_permission_customer_group_list')
            ->setAttribute('iconCls', 'coreshop_nav_icon_customer_groups')
            ->setAttribute('resource', 'coreshop.customer')
            ->setAttribute('function', 'customer_groups')
            ->setExtra('order', 20);

        $customersMenu
            ->addChild('coreshop_customer_to_company_assign_to_new')
            ->setLabel('coreshop_customer_to_company_assign_to_new')
            ->setAttribute('permission', 'coreshop_permission_ctc_assign_to_new')
            ->setAttribute('iconCls', 'coreshop_nav_icon_customer_to_company_assign_to_new')
            ->setAttribute('resource', 'coreshop.core')
            ->setAttribute('function', 'customer_to_company_assign_to_new')
            ->setExtra('order', 30);

        $customersMenu
            ->addChild('coreshop_customer_to_company_assign_to_existing')
            ->setLabel('coreshop_customer_to_company_assign_to_existing')
            ->setAttribute('permission', 'coreshop_permission_ctc_assign_to_existing')
            ->setAttribute('iconCls', 'coreshop_nav_icon_customer_to_company_assign_to_existing')
            ->setAttribute('resource', 'coreshop.core')
            ->setAttribute('function', 'customer_to_company_assign_to_existing')
            ->setExtra('order', 40);

        $menuItem->addChild('coreshop_notification_rules')
            ->setLabel('coreshop_notification_rules')
            ->setAttribute('permission', 'coreshop_permission_notification')
            ->setAttribute('iconCls', 'coreshop_nav_icon_notification_rule')
            ->setAttribute('resource', 'coreshop.notification')
            ->setAttribute('function', 'notification_rule')
            ->setExtra('order', 80);

        $menuItem->addChild('coreshop_payment_providers')
            ->setLabel('coreshop_payment_providers')
            ->setAttribute('permission', 'coreshop_permission_payment_provider')
            ->setAttribute('iconCls', 'coreshop_nav_icon_payment_provider')
            ->setAttribute('resource', 'coreshop.payment')
            ->setAttribute('function', 'payment_provider')
            ->setExtra('order', 90);

        $menuItem->addChild('coreshop_stores')
            ->setLabel('coreshop_stores')
            ->setAttribute('permission', 'coreshop_permission_store')
            ->setAttribute('iconCls', 'coreshop_nav_icon_store')
            ->setAttribute('resource', 'coreshop.store')
            ->setAttribute('function', 'store')
            ->setExtra('order', 100);

        $menuItem->addChild('coreshop_about')
            ->setLabel('coreshop_about')
            ->setAttribute('iconCls', 'coreshop_nav_icon_logo')
            ->setAttribute('resource', 'coreshop.core')
            ->setAttribute('function', 'about')
            ->setExtra('order', 1000);
    }
}
