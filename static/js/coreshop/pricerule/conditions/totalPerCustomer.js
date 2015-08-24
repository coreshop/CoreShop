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

        var totalAvailableValue = 0;

        if(!isNaN(this.data)) {
            totalAvailableValue = this.data.totalAvailable;
        }

        var totalAvailable = new Ext.ux.form.SpinnerField({
            fieldLabel:t("coreshop_condition_totalPerCustomer_total"),
            name:'total',
            value : totalAvailableValue,
            minValue : 0,
            decimalPrecision : 0
        });

        this.form = new Ext.form.FieldSet({
            items : [
                totalAvailable
            ]
        });

        return this.form;
    }
});