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

pimcore.registerNS("pimcore.plugin.coreshop.pricerules.conditions.totalPerCustomer");

pimcore.plugin.coreshop.pricerules.conditions.totalPerCustomer = Class.create(pimcore.plugin.coreshop.pricerules.conditions.abstract, {

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