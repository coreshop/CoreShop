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

pimcore.registerNS('coreshop.shippingrule.conditions.countries');
coreshop.shippingrule.conditions.countries = Class.create(coreshop.rules.conditions.abstract, {
    type: 'countries',

    getForm: function () {
        var me = this;
        var store = pimcore.globalmanager.get('coreshop_countries');

        var countries = {
            fieldLabel: t('coreshop_condition_countries'),
            typeAhead: true,
            listWidth: 100,
            width: 500,
            store: store,
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect: true,
            triggerAction: 'all',
            name: 'countries',
            height: 400,
            delimiter: false,
            listeners: {
                beforerender: function () {
                    if (!store.isLoaded() && !store.isLoading())
                        store.load();

                    if (me.data && me.data.countries)
                        this.setValue(me.data.countries);
                }
            }
        };


        if (this.data && this.data.countries) {
            countries.value = this.data.countries;
        }

        countries = new Ext.ux.form.MultiSelect(countries);

        this.form = new Ext.form.Panel({
            items: [
                countries
            ]
        });

        return this.form;
    }
});
