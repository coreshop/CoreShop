/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.cart.pricerules.actions.cartItemAction');
coreshop.cart.pricerules.actions.cartItemAction = Class.create(coreshop.rules.actions.abstract, {

    type: 'cartItemAction',

    operatorCombo: null,
    conditions: null,

    getForm: function () {
        var me = this,
            panel =  new Ext.TabPanel({
            items: [

            ]
        });

        panel.setLoading(t('loading'));

        this.conditions = null;
        this.actions = null;

        Ext.Ajax.request({
            url: Routing.generate('coreshop_cart_price_rule_getCartItemConfig'),
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);

                me.conditions = new coreshop.cart_item.pricerules.condition(config.conditions);
                me.actions = new coreshop.cart_item.pricerules.action(config.actions);

                panel.add(me.conditions.getLayout());
                panel.add(me.actions.getLayout());
                panel.setActiveTab(0);

                // add saved conditions
                if (me.data && me.data.conditions) {
                    Ext.each(me.data.conditions, function (condition) {
                        me.conditions.addCondition(condition.type, condition, false);
                    }.bind(me));
                }

                // add saved conditions
                if (me.data && me.data.actions) {
                    Ext.each(me.data.actions, function (action) {
                        me.actions.addAction(action.type, action, false);
                    }.bind(me));
                }

                panel.setLoading(false);
            }
        });


        this.form = new Ext.form.Panel({
            items: [
                panel
            ]
        });

        return this.form;
    },

    getValues: function () {
        return {
            conditions: this.conditions ? this.conditions.getConditionsData() : null,
            actions: this.actions ? this.actions.getActionsData() : null,
        };
    }
});
