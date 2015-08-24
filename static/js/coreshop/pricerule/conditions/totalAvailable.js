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


pimcore.registerNS("pimcore.plugin.coreshop.pricerule.conditions.totalAvailable");
pimcore.plugin.coreshop.pricerule.conditions.totalAvailable = Class.create(pimcore.plugin.coreshop.pricerule.conditions.abstract, {

    type : 'totalAvailable',

    getForm : function() {

        var totalAvailableValue = 0;
        var totalUsedValue = 0;

        if(!isNaN(this.data)) {
            totalAvailableValue = this.data.totalAvailable;
            totalUsedValue = this.data.totalUsed;
        }

        var totalAvailable = new Ext.ux.form.SpinnerField({
            fieldLabel:t("coreshop_condition_totalAvailable_totalAvailable"),
            name:'totalAvailable',
            value : totalAvailableValue,
            minValue : 0,
            decimalPrecision : 0
        });

        var totalUsed = new Ext.form.TextField({
            fieldLabel:t("coreshop_condition_totalAvailable_totalUsed"),
            name:'totalUsed',
            value : totalUsedValue,
            minValue : 0,
            decimalPrecision : 0
        });


        this.form = new Ext.form.FieldSet({
            items : [
                totalAvailable, totalUsed
            ]
        });

        return this.form;
    }
});