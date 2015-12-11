/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */


pimcore.registerNS("pimcore.plugin.coreshop.pricerule.actions.discountAmount");
pimcore.plugin.coreshop.pricerule.actions.discountAmount = Class.create(pimcore.plugin.coreshop.pricerule.actions.abstract, {

    type : 'discountAmount',

    getForm : function() {
        var amountValue = 0;
        var currencyValue = null;
        var me = this;

        if(this.data) {
            amountValue = this.data.amount;
            currencyValue = this.data.currency;
        }

        var amount = new Ext.form.NumberField({
            fieldLabel:t("coreshop_action_discountAmount_amount"),
            name:'amount',
            value : amountValue,
            minValue : 0,
            decimalPrecision : 0,
            step : 1
        });

        var currency = {
            xtype: 'combo',
            fieldLabel: t('coreshop_action_discountAmount_currency'),
            typeAhead: true,
            value: currencyValue,
            mode: 'local',
            listWidth: 100,
            width : 200,
            store: pimcore.globalmanager.get("coreshop_currencies"),
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            hiddenName:'currency',
            listeners: {
                beforerender: function () {
                    this.setValue(me.data.currency);
                }
            }
        };

        this.form = new Ext.form.FieldSet({
            items : [
                amount, currency
            ]
        });

        return this.form;
    }
});
