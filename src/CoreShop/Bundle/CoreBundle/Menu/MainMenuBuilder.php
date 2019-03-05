<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Menu;

use CoreShop\Bundle\MenuBundle\Builder\MenuBuilderInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MainMenuBuilder implements MenuBuilderInterface
{
    public function buildMenu(ItemInterface $menuItem, FactoryInterface $factory, string $type)
    {
        $menuItem->setLabel('coreshop');
        $menuItem->setAttributes([
            'content' =>
                '<svg version="1.1" id="Ebene_1" xmlns="http://www.w3.org/2000/svg"  x="0px" y="0px"'.
                'width="61.3" height="84.6" viewBox="0 0 61.3 84.6" enable-background="new 0 0 61.3 84.6" xml:space="preserve">'.
                '<style type="text/css">'.
                '.st0{display:none;}'.
                '.st1{display:inline;fill:#969696;}'.
                '.st2{display:inline;fill:white;}'.
                '.st3{fill:#969696;}'.
                '.st4{fill:white;}'.
                '</style>'.
                '<g>'.
                '<g class="st0">'.
                '<path class="st1" d="M7.4,113.2c1.6,0,2.9-0.6,3.8-1.9l2,2.1c-1.6,1.8-3.5,2.7-5.7,2.7c-2.2,0-4-0.7-5.4-2.1'.
                'C0.7,112.7,0,111,0,108.8c0-2.1,0.7-3.9,2.2-5.3c1.5-1.4,3.2-2.1,5.3-2.1c2.3,0,4.3,0.9,5.9,2.7l-2,2.3c-1-1.3-2.3-1.9-3.8-1.9'.
                'c-1.2,0-2.2,0.4-3.1,1.2c-0.9,0.8-1.3,1.8-1.3,3.2c0,1.3,0.4,2.4,1.2,3.2C5.3,112.8,6.3,113.2,7.4,113.2z"/>'.
                '<path class="st1" d="M26.1,110.5c0,1.6-0.6,2.9-1.7,4c-1.1,1.1-2.5,1.6-4.2,1.6s-3.1-0.5-4.2-1.6c-1.1-1.1-1.7-2.4-1.7-4'.
                'c0-1.6,0.6-2.9,1.7-4c1.1-1.1,2.5-1.6,4.2-1.6s3.1,0.5,4.2,1.6C25.6,107.6,26.1,108.9,26.1,110.5z M17.6,110.5'.
                'c0,0.9,0.3,1.6,0.8,2.2c0.5,0.6,1.2,0.8,2,0.8s1.5-0.3,2-0.8c0.5-0.6,0.8-1.3,0.8-2.2c0-0.9-0.3-1.6-0.8-2.2'.
                'c-0.5-0.6-1.2-0.9-2-0.9s-1.5,0.3-2,0.9C17.8,108.9,17.6,109.6,17.6,110.5z"/>'.
                '<path class="st1" d="M34.2,107.7c-0.9,0-1.6,0.3-2,1c-0.5,0.6-0.7,1.5-0.7,2.6v4.8h-3.1v-11h3.1v1.5c0.4-0.5,0.109-0.8,1.5-1.1'.
                'c0.6-0.3,1.2-0.5,1.8-0.5l0,2.9H34.2z"/>'.
                '<path class="st1" d="M46.3,114.4c-1.2,1.2-2.7,1.8-4.4,1.8s-3.1-0.5-4.1-1.5c-1.1-1-1.6-2.4-1.6-4.1c0-1.7,0.6-3.1,1.7-4.1'.
                'c1.1-1,2.4-1.5,3.9-1.5c1.5,0,2.8,0.5,3.9,1.4c1.1,0.9,1.6,2.2,1.6,3.8v1.6h-8c0.1,0.6,0.4,1.1,0.9,1.5c0.5,0.4,1.1,0.6,1.8,0.6'.
                'c1.1,0,2-0.4,2.7-1.1L46.3,114.4z M43.4,107.9c-0.4-0.4-0.9-0.5-1.5-0.5c-0.6,0-1.2,0.2-1.7,0.6c-0.5,0.4-0.8,0.9-0.9,1.5h4.8'.
                'C44,108.8,43.8,108.3,43.4,107.9z"/>'.
                '<path class="st2" d="M52.9,104.6c-0.3,0.3-0.5,0.6-0.5,1s0.2,0.7,0.6,1c0.4,0.2,1.2,0.5,2.6,0.9c1.4,0.3,2.4,0.8,3.2,1.5'.
                'c0.8,0.7,1.1,1.6,1.1,2.9c0,1.3-0.5,2.3-1.4,3.1c-1,0.8-2.2,1.2-3.8,1.2c-2.3,0-4.3-0.8-6.1-2.5l1.9-2.3c1.5,1.3,3,2,4.3,2'.
                'c0.6,0,1-0.1,1.4-0.4c0.3-0.3,0.5-0.6,0.5-1c0-0.4-0.2-0.8-0.5-1c-0.4-0.3-1.1-0.5-2.1-0.8c-1.7-0.4-2.9-0.9-3.7-1.5'.
                'c-0.8-0.6-1.2-1.6-1.2-3c0-1.4,0.5-2.4,1.5-3.1c1-0.7,2.2-1.1,3.7-1.1c1,0,1.9,0.2,2.9,0.5c1,0.3,1.8,0.8,2.5,1.4l-1.6,2.3'.
                'c-1.2-0.9-2.5-1.4-3.8-1.4C53.6,104.2,53.2,104.3,52.9,104.6z"/>'.
                '<path class="st2" d="M65.4,110.1v5.9h-3.1v-15.2h3.1v5.4c0.9-0.9,2-1.4,3.1-1.4s2.1,0.4,2.9,1.2c0.8,0.8,1.2,1.9,1.2,3.3v6.7h-3.1'.
                'v-6c0-1.7-0.6-2.5-1.9-2.5c-0.6,0-1.1,0.2-1.6,0.7C65.6,108.6,65.4,109.2,65.4,110.1z"/>'.
                '<path class="st2" d="M86.4,110.5c0,1.6-0.6,2.9-1.7,4c-1.1,1.1-2.5,1.6-4.2,1.6c-1.7,0-3.1-0.5-4.2-1.6c-1.1-1.1-1.7-2.4-1.7-4'.
                'c0-1.6,0.6-2.9,1.7-4c1.1-1.1,2.5-1.6,4.2-1.6c1.7,0,3.1,0.5,4.2,1.6C85.8,107.6,86.4,108.9,86.4,110.5z M77.8,110.5'.
                'c0,0.9,0.3,1.6,0.8,2.2c0.5,0.6,1.2,0.8,2,0.8s1.5-0.3,2-0.8c0.5-0.6,0.8-1.3,0.8-2.2c0-0.9-0.3-1.6-0.8-2.2'.
                'c-0.5-0.6-1.2-0.9-2-0.9s-1.5,0.3-2,0.9C78.1,108.9,77.8,109.6,77.8,110.5z"/>'.
                '<path class="st2" d="M95.1,104.8c1.3,0,2.4,0.5,3.4,1.6c1,1.1,1.5,2.4,1.5,4c0,1.6-0.5,3-1.5,4.1c-1,1.1-2.2,1.6-3.5,1.6'.
                'c-1.3,0-2.4-0.5-3.2-1.6v5.4h-3.1v-15h3.1v1.2C92.7,105.3,93.8,104.8,95.1,104.8z M91.7,110.5c0,0.9,0.2,1.6,0.7,2.2'.
                'c0.5,0.6,1.1,0.8,1.8,0.8c0.7,0,1.3-0.3,1.9-0.8c0.5-0.6,0.8-1.3,0.8-2.2c0-0.9-0.3-1.6-0.8-2.2c-0.5-0.6-1.1-0.9-1.9-0.9'.
                's-1.3,0.3-1.8,0.9C91.9,108.9,91.7,109.6,91.7,110.5z"/>'.
                '</g>'.
                '<g>'.
                '<path id="Shake" class="st3" d="M60.8,33.7c0-0.1-8.8-20.3-8.8-20.3c-0.7-1.3-1.4-2-2.7-2.5c-1.3-0.6-2.2-0.7-3.7-0.4l-2.9,0.9'.
                'c0,1.7,0.2,3.4,0.6,4.7c0,0.1,0.1,0.2,0.1,0.3c0.8-1.3,2.5-1.9,4-1.2c1.6,0.7,2.3,2.6,1.6,4.2c-0.7,1.6-2.6,2.3-4.2,1.5'.
                'c-1-0.5-1.7-1.4-1.8-2.4c0,0,0,0,0,0c-1.3-2.2-1.4-5.6-1.4-6.7l-16.7,5.2c-2.3,1.1-4.1,2-5.2,4.3c-0.8,1.8-4.2,9.1-7.9,17.2'.
                'L3.3,56.8C1.5,60.5,0.4,63,0.4,63c-1,2.3,0,5,2.2,6c0,0,29.8,13.7,33,15.2c2.3,1,5,0,6-2.2l9.9-21.6l0.8-1.7l8.5-18.5'.
                'c0.8-1.8,0.7-3.4,0.3-5.1"/>'.
                '<g>'.
                '<path class="st4" d="M47.5,0c-1.8-0.1-2.9,1.8-3.4,3.3c-0.8,2-1.2,4.1-1.3,6.3c-0.2,2.4,0,4.7,0.5,6.5c0.4,1.3,1.1,3.1,2.6,3.4'.
                'c0.7,0.2,1.5-0.1,2-0.5c0.5-0.4,0.9-1,1.1-1.3c0.1-0.1,0.1-0.2,0.1-0.2c-0.1-0.9-0.7-1.7-1.5-2.2c-0.2,0.4-0.3,0.8-0.5,1.1'.
                'c-0.1,0.2-0.5,1-0.9,0.8c-0.5-0.2-0.8-1.7-1-2.3c-0.3-1.3-0.5-3-0.3-5.2c0.3-4.4,1.6-7.1,2.3-7.4c0.5,0.2,1.6,2.5,1.4,6.9'.
                'c0,0,1.1,0.3,2.2,1c0-0.1,0.2-2-0.1-4.2c-0.2-1.8-0.5-3.9-1.9-5.3C48.6,0.3,48.1,0,47.5,0z"/>'.
                '</g>'.
                '</g>'.
                '</g>'.
                '</svg>',
        ]);


        $menuItem->addChild('coreshop_order_by_number')
            ->setLabel('coreshop_order_by_number')
            ->setAttribute('permission', 'coreshop_permission_order_detail')
            ->setAttribute('iconCls', 'coreshop_icon_order')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'open_order_by_number');

        $menuItem->addChild('coreshop_quote_by_number')
            ->setLabel('coreshop_quote_by_number')
            ->setAttribute('permission', 'coreshop_permission_quote_detail')
            ->setAttribute('iconCls', 'coreshop_icon_quote')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'coreshop_quote_by_number');

        $menuItem->addChild('coreshop_settings')
            ->setLabel('coreshop_settings')
            ->setAttribute('permission', 'coreshop_permission_settings')
            ->setAttribute('iconCls', 'coreshop_icon_settings')
            ->setAttribute('resource', 'coreshop.core')
            ->setAttribute('function', 'settings');

        $priceRules = $menuItem
            ->addChild('coreshop_pricerules')
            ->setLabel('coreshop_pricerules')
            ->setAttribute('iconCls', 'coreshop_icon_price_rule')
            ->setAttribute('container', true);

        $priceRules
            ->addChild('coreshop_cart_pricerules')
            ->setLabel('coreshop_cart_pricerules')
            ->setAttribute('permission', 'coreshop_permission_cart_price_rule')
            ->setAttribute('iconCls', 'coreshop_icon_price_rule')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'cart_price_rule');

        $priceRules
            ->addChild('coreshop_product_pricerules')
            ->setLabel('coreshop_product_pricerules')
            ->setAttribute('permission', 'coreshop_permission_product_price_rule')
            ->setAttribute('iconCls', 'coreshop_icon_price_rule')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'product_price_rule');

        $localization = $menuItem
            ->addChild('coreshop_localization')
            ->setLabel('coreshop_localization')
            ->setAttribute('iconCls', 'coreshop_icon_localization')
            ->setAttribute('container', true);

        $localization
            ->addChild('coreshop_countries')
            ->setLabel('coreshop_countries')
            ->setAttribute('permission', 'coreshop_permission_country')
            ->setAttribute('iconCls', 'coreshop_icon_country')
            ->setAttribute('resource', 'coreshop.address')
            ->setAttribute('function', 'country');

        $localization
            ->addChild('coreshop_states')
            ->setLabel('coreshop_states')
            ->setAttribute('permission', 'coreshop_permission_state')
            ->setAttribute('iconCls', 'coreshop_icon_state')
            ->setAttribute('resource', 'coreshop.address')
            ->setAttribute('function', 'state');

        $localization
            ->addChild('coreshop_currencies')
            ->setLabel('coreshop_currencies')
            ->setAttribute('permission', 'coreshop_permission_currency')
            ->setAttribute('iconCls', 'coreshop_icon_currency')
            ->setAttribute('resource', 'coreshop.currency')
            ->setAttribute('function', 'currency');

        $localization
            ->addChild('coreshop_exchange_rates')
            ->setLabel('coreshop_exchange_rates')
            ->setAttribute('permission', 'coreshop_permission_exchange_rate')
            ->setAttribute('iconCls', 'coreshop_icon_exchange_rate')
            ->setAttribute('resource', 'coreshop.currency')
            ->setAttribute('function', 'exchange_rate');

        $localization
            ->addChild('coreshop_zones')
            ->setLabel('coreshop_zones')
            ->setAttribute('permission', 'coreshop_permission_zone')
            ->setAttribute('iconCls', 'coreshop_icon_zone')
            ->setAttribute('resource', 'coreshop.address')
            ->setAttribute('function', 'zone');

        $localization
            ->addChild('coreshop_taxes')
            ->setLabel('coreshop_taxes')
            ->setAttribute('permission', 'coreshop_permission_tax_item')
            ->setAttribute('iconCls', 'coreshop_icon_taxes')
            ->setAttribute('resource', 'coreshop.taxation')
            ->setAttribute('function', 'tax_item');

        $localization
            ->addChild('coreshop_taxrulegroups')
            ->setLabel('coreshop_taxrulegroups')
            ->setAttribute('permission', 'coreshop_permission_tax_rule_group')
            ->setAttribute('iconCls', 'coreshop_icon_tax_rule_groups')
            ->setAttribute('resource', 'coreshop.taxation')
            ->setAttribute('function', 'tax_rule_group');

        $ordersMenu = $menuItem
            ->addChild('coreshop_order')
            ->setLabel('coreshop_order')
            ->setAttribute('iconCls', 'coreshop_icon_order')
            ->setAttribute('container', true);

        $ordersMenu
            ->addChild('coreshop_orders')
            ->setLabel('coreshop_orders')
            ->setAttribute('permission', 'coreshop_permission_order_list')
            ->setAttribute('iconCls', 'coreshop_icon_orders')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'orders');

        $ordersMenu
            ->addChild('coreshop_order_create')
            ->setLabel('coreshop_order_create')
            ->setAttribute('permission', 'coreshop_permission_order_create')
            ->setAttribute('iconCls', 'coreshop_icon_order_create')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'create_order');

        $ordersMenu
            ->addChild('coreshop_quotes')
            ->setLabel('coreshop_quotes')
            ->setAttribute('permission', 'coreshop_permission_quote_list')
            ->setAttribute('iconCls', 'coreshop_icon_quotes')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'quotes');

        $ordersMenu
            ->addChild('coreshop_quote_create')
            ->setLabel('coreshop_quote_create')
            ->setAttribute('permission', 'coreshop_permission_quote_create')
            ->setAttribute('iconCls', 'coreshop_icon_quote_create')
            ->setAttribute('resource', 'coreshop.order')
            ->setAttribute('function', 'create_quote');

        $carriersMenu = $menuItem
            ->addChild('coreshop_shipping')
            ->setLabel('coreshop_shipping')
            ->setAttribute('iconCls', 'coreshop_icon_shipping')
            ->setAttribute('container', true);

        $carriersMenu
            ->addChild('coreshop_carriers')
            ->setLabel('coreshop_carriers')
            ->setAttribute('permission', 'coreshop_permission_carrier')
            ->setAttribute('iconCls', 'coreshop_icon_carriers')
            ->setAttribute('resource', 'coreshop.shipping')
            ->setAttribute('function', 'carrier');

        $carriersMenu
            ->addChild('coreshop_carriers_shipping_rules')
            ->setLabel('coreshop_carriers_shipping_rules')
            ->setAttribute('permission', 'coreshop_permission_shipping_rule')
            ->setAttribute('iconCls', 'coreshop_icon_carrier_shipping_rule')
            ->setAttribute('resource', 'coreshop.shipping')
            ->setAttribute('function', 'shipping_rules');

        $productsMenu = $menuItem
            ->addChild('coreshop_product')
            ->setLabel('coreshop_product')
            ->setAttribute('iconCls', 'coreshop_icon_product')
            ->setAttribute('container', true);

        $productsMenu
            ->addChild('coreshop_indexes')
            ->setLabel('coreshop_indexes')
            ->setAttribute('permission', 'coreshop_permission_index')
            ->setAttribute('iconCls', 'coreshop_icon_carriers')
            ->setAttribute('resource', 'coreshop.index')
            ->setAttribute('function', 'index');

        $productsMenu
            ->addChild('coreshop_product_units')
            ->setLabel('coreshop_product_units')
            ->setAttribute('permission', 'coreshop_product_unit')
            ->setAttribute('iconCls', 'coreshop_icon_product_units')
            ->setAttribute('resource', 'coreshop.index')
            ->setAttribute('function', 'product_unit');

        $productsMenu
            ->addChild('coreshop_filters')
            ->setLabel('coreshop_filters')
            ->setAttribute('permission', 'coreshop_permission_filter')
            ->setAttribute('iconCls', 'coreshop_icon_filters')
            ->setAttribute('resource', 'coreshop.index')
            ->setAttribute('function', 'filter');


        $menuItem->addChild('coreshop_notification_rules')
            ->setLabel('coreshop_notification_rules')
            ->setAttribute('permission', 'coreshop_permission_notification')
            ->setAttribute('iconCls', 'coreshop_icon_notification_rule')
            ->setAttribute('resource', 'coreshop.notification')
            ->setAttribute('function', 'notification_rule');

        $menuItem->addChild('coreshop_payment_providers')
            ->setLabel('coreshop_payment_providers')
            ->setAttribute('permission', 'coreshop_permission_payment_provider')
            ->setAttribute('iconCls', 'coreshop_icon_payment_provider')
            ->setAttribute('resource', 'coreshop.payment')
            ->setAttribute('function', 'payment_provider');

        $menuItem->addChild('coreshop_stores')
            ->setLabel('coreshop_stores')
            ->setAttribute('permission', 'coreshop_permission_store')
            ->setAttribute('iconCls', 'coreshop_icon_store')
            ->setAttribute('resource', 'coreshop.store')
            ->setAttribute('function', 'store');


        $menuItem->addChild('coreshop_about')
            ->setLabel('coreshop_about')
            ->setAttribute('iconCls', 'coreshop_icon_logo')
            ->setAttribute('resource', 'coreshop.core')
            ->setAttribute('function', 'about');
    }
}
