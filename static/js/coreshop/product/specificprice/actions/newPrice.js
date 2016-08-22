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

pimcore.registerNS('pimcore.plugin.coreshop.pricerules.actions.newPrice');

pimcore.plugin.coreshop.rules.actions.newPrice = Class.create(pimcore.plugin.coreshop.rules.actions.abstract, {

    type : 'newPrice',

    getForm : function () {
        var newPriceValue = 0;
        var currencyValue = null;
        var me = this;

        if (this.data) {
            newPriceValue = this.data.newPrice;
            currencyValue = this.data.currency;
        }

        var newPrice = new Ext.form.NumberField({
            fieldLabel:t('coreshop_action_newPrice'),
            name:'newPrice',
            value : newPriceValue,
            decimalPrecision : 2
        });

        var currency = {
            xtype: 'combo',
            fieldLabel: t('coreshop_action_discountAmount_currency'),
            typeAhead: true,
            value: currencyValue,
            mode: 'local',
            listWidth: 100,
            width : 200,
            store: pimcore.globalmanager.get('coreshop_currencies'),
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

        this.form = new Ext.form.Panel({
            items : [
                newPrice, currency
            ]
        });

        return this.form;
    }
});
