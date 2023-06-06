/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.paymentproviderrule.conditions.currencies');
coreshop.paymentproviderrule.conditions.currencies = Class.create(coreshop.rules.conditions.abstract, {
    type: 'currencies',

    getForm: function () {
        var me = this;

        var currencies = {
            fieldLabel: t('coreshop_condition_currencies'),
            typeAhead: true,
            listWidth: 100,
            width: 500,
            store: {
                type: 'coreshop_currencies'
            },
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect: true,
            triggerAction: 'all',
            name: 'currencies',
            maxHeight: 400,
            delimiter: false,
            value: me.data.currencies
        };

        if (this.data && this.data.currencies) {
            currencies.value = this.data.currencies;
        }

        currencies = new Ext.ux.form.MultiSelect(currencies);

        this.form = new Ext.form.Panel({
            items: [
                currencies
            ]
        });

        return this.form;
    }
});
