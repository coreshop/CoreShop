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

pimcore.registerNS('pimcore.plugin.coreshop.pricerules.actions.newPrice');

pimcore.plugin.coreshop.pricerules.actions.newPrice = Class.create(pimcore.plugin.coreshop.pricerules.actions.abstract, {

    type : 'newPrice',

    getForm : function () {
        var newPriceValue = 0;
        var me = this;

        if (this.data) {
            newPriceValue = this.data.newPrice;
        }

        var newPrice = new Ext.form.NumberField({
            fieldLabel:t('coreshop_action_newPrice'),
            name:'newPrice',
            value : newPriceValue,
            decimalPrecision : 2
        });

        this.form = new Ext.form.FieldSet({
            items : [
                newPrice
            ]
        });

        return this.form;
    }
});
