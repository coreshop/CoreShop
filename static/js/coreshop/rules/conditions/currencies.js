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

pimcore.registerNS('pimcore.plugin.coreshop.rules.conditions.currencies');

pimcore.plugin.coreshop.rules.conditions.currencies = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {

    type : 'currencies',

    getForm : function () {
        var me = this;
        var store = pimcore.globalmanager.get('coreshop_currencies');

        var currencies = {
            fieldLabel: t('coreshop_condition_currencies'),
            typeAhead: true,
            listWidth: 100,
            width : 500,
            store: store,
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect : true,
            triggerAction: 'all',
            name:'currencies',
            maxHeight : 400,
            listeners: {
                beforerender: function () {
                    if (!store.isLoaded() && !store.isLoading())
                        store.load();

                    if (me.data && me.data.currencies)
                        this.setValue(me.data.currencies);
                }
            }
        };

        currencies = new Ext.ux.form.MultiSelect(currencies);

        if (this.data && this.data.currencies) {
            currencies.value = this.data.currencies;
        }

        this.form = new Ext.form.FieldSet({
            items : [
                currencies
            ]
        });

        return this.form;
    }
});
