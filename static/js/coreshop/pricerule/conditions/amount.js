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


pimcore.registerNS("pimcore.plugin.coreshop.pricerule.conditions.amount");
pimcore.plugin.coreshop.pricerule.conditions.amount = Class.create(pimcore.plugin.coreshop.pricerule.conditions.abstract, {

    type : 'amount',

    getForm : function() {

        var minAmountValue = 0;
        var currencyValue = null;
        var me = this;

        if(this.data && this.data.minAmount) {
            minAmountValue = this.data.minAmount;
            currencyValue = this.data.currency;
        }

        var minAmount = new Ext.form.NumberField({
            fieldLabel:t("coreshop_condition_amount_minAmount"),
            name:'minAmount',
            value : minAmountValue,
            minValue : 0,
            decimalPrecision : 0,
            step : 1
        });

        var currency = {
            xtype: 'combo',
            fieldLabel: t('coreshop_condition_amount_currency'),
            typeAhead: true,
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
                listeners: {
                    beforerender: function () {
                        this.setValue(me.data.currency);
                    }
                }
            }
        };

        if(this.data && this.data.currency) {
            currency.value = this.data.currency;
        }


        this.form = Ext.create("Ext.form.FieldSet", {
            items : [
                minAmount, currency
            ]
        });

        return this.form;
    }
});
