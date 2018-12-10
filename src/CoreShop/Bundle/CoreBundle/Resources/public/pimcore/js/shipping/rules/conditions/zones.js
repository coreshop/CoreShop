/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.shippingrule.conditions.zones');
coreshop.shippingrule.conditions.zones = Class.create(coreshop.rules.conditions.abstract, {
    type: 'zones',

    getForm: function () {
        var me = this;
        var store = pimcore.globalmanager.get('coreshop_zones');

        var zones = {
            fieldLabel: t('coreshop_condition_zones'),
            typeAhead: true,
            listWidth: 100,
            width: 500,
            store: store,
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect: true,
            triggerAction: 'all',
            name: 'zones',
            maxHeight: 400,
            delimiter: false,
            listeners: {
                beforerender: function () {
                    if (!store.isLoaded() && !store.isLoading())
                        store.load();

                    if (me.data && me.data.zones)
                        this.setValue(me.data.zones);
                }
            }
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
