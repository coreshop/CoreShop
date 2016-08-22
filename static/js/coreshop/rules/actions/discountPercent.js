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

pimcore.registerNS('pimcore.plugin.coreshop.rules.actions.discountPercent');

pimcore.plugin.coreshop.rules.actions.discountPercent = Class.create(pimcore.plugin.coreshop.rules.actions.abstract, {

    type : 'discountPercent',

    getForm : function () {
        var percentValue = 0;
        var currencyValue = null;
        var me = this;

        if (this.data) {
            percentValue = this.data.percent;
            currencyValue = this.data.currency;
        }

        var percent = new Ext.form.NumberField({
            fieldLabel:t('coreshop_action_discountPercent_percent'),
            name:'percent',
            value : percentValue,
            minValue : 0,
            maxValue : 100,
            decimalPrecision : 0
        });

        var currency = {
            xtype: 'combo',
            fieldLabel: t('coreshop_action_discountPercent_currency'),
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
            name:'currency',
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
                percent, currency
            ]
        });

        return this.form;
    }
});
