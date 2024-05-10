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

pimcore.registerNS('coreshop.paymentproviderrule.conditions.zones');
coreshop.paymentproviderrule.conditions.zones = Class.create(coreshop.rules.conditions.abstract, {
    type: 'zones',

    getForm: function () {
        var me = this;

        var zones = {
            fieldLabel: t('coreshop_condition_zones'),
            typeAhead: true,
            listWidth: 100,
            width: 500,
            store: {
                type: 'coreshop_zones'
            },
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect: true,
            triggerAction: 'all',
            name: 'zones',
            maxHeight: 400,
            delimiter: false,
            value: me.data.zones
        };

        if (this.data && this.data.zones) {
            zones.value = this.data.zones;
        }

        zones = new Ext.ux.form.MultiSelect(zones);

        this.form = new Ext.form.Panel({
            items: [
                zones
            ]
        });

        return this.form;
    }
});
