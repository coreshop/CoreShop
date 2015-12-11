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


pimcore.registerNS("pimcore.plugin.coreshop.pricerule.conditions.totalPerCustomer");
pimcore.plugin.coreshop.pricerule.conditions.totalPerCustomer = Class.create(pimcore.plugin.coreshop.pricerule.conditions.abstract, {

    type : 'totalPerCustomer',

    getForm : function() {

        var totalValue  = 0;

        if(this.data) {
            totalValue = this.data.total;
        }

        var total = new Ext.form.NumberField({
            fieldLabel:t("coreshop_condition_totalPerCustomer_total"),
            name:'total',
            value : totalValue ,
            minValue : 0,
            decimalPrecision : 0,
            step : 1
        });

        this.form = new Ext.form.FieldSet({
            items : [
                total
            ]
        });

        return this.form;
    }
});