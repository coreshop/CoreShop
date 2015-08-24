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

        if(!isNaN(this.data.minAmount)) {
            minAmountValue = this.data.minAmount;
        }

        var minAmount = new Ext.ux.form.SpinnerField({
            fieldLabel:t("coreshop_condition_amount_minAmount"),
            name:'minAmount',
            value : minAmountValue,
            minValue : 0,
            decimalPrecision : 0
        });

        var currency = {
            xtype: 'combo',
            fieldLabel: t('coreshop_condition_amount_currency'),
            typeAhead: true,
            value: this.data.currency,
            mode: 'local',
            listWidth: 100,
            store: pimcore.globalmanager.get("coreshop_currencies"),
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            hiddenName:'currency',
            listeners: {
                change: function () {
                    this.forceReloadOnSave = true;
                }.bind(this),
                select: function () {
                    this.forceReloadOnSave = true;
                }.bind(this)
            }
        };


        this.form = new Ext.form.FieldSet({
            items : [
                minAmount, currency
            ]
        });

        return this.form;
    }
});
