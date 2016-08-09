/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.carrier.shippingrules.actions.fixedPrice');

pimcore.plugin.coreshop.carrier.shippingrules.actions.fixedPrice = Class.create(pimcore.plugin.coreshop.rules.actions.abstract, {

    type : 'fixedPrice',

    getForm : function () {
        var fixedPriceValue = 0;
        var me = this;

        if (this.data) {
            fixedPriceValue = this.data.fixedPrice;
        }

        var fixedPrice = new Ext.form.NumberField({
            fieldLabel:t('coreshop_action_fixedPrice'),
            name:'fixedPrice',
            value : fixedPriceValue,
            decimalPrecision : 2
        });

        this.form = new Ext.form.FieldSet({
            items : [
                fixedPrice
            ]
        });

        return this.form;
    }
});
