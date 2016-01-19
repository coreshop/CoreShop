/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */


pimcore.registerNS("pimcore.plugin.coreshop.pricerule.conditions.totalAvailable");
pimcore.plugin.coreshop.pricerule.conditions.totalAvailable = Class.create(pimcore.plugin.coreshop.pricerule.conditions.abstract, {

    type : 'totalAvailable',

    getForm : function() {

        var totalAvailableValue = 0;
        var totalUsedValue = 0;

        if(this.data) {
            totalAvailableValue = this.data.totalAvailable;
            totalUsedValue = this.data.totalUsed;
        }

        var totalAvailable = new Ext.form.NumberField({
            fieldLabel:t("coreshop_condition_totalAvailable_totalAvailable"),
            name:'totalAvailable',
            value : totalAvailableValue,
            minValue : 0,
            decimalPrecision : 0,
            step : 1
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