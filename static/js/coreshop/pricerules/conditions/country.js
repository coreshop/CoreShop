/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.pricerules.conditions.country');

pimcore.plugin.coreshop.pricerules.conditions.country = Class.create(pimcore.plugin.coreshop.pricerules.conditions.abstract, {

    type : 'country',

    getForm : function () {
        var me = this;
        var store = pimcore.globalmanager.get('coreshop_countries');

        var country = {
            xtype: 'combo',
            fieldLabel: t('coreshop_condition_country_country'),
            typeAhead: true,
            listWidth: 100,
            width : 500,
            store: store,
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            name:'country',
            listeners: {
                beforerender: function () {
                    if (!store.isLoaded() && !store.isLoading())
                        store.load();

                    if (me.data && me.data.country)
                        this.setValue(me.data.country);
                }
            }
        };

        if (this.data && this.data.country) {
            country.value = this.data.country;
        }

        this.form = new Ext.form.FieldSet({
            items : [
                country
            ]
        });

        return this.form;
    }
});
