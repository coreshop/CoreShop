/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
*/

pimcore.registerNS('pimcore.plugin.coreshop.rules.conditions.shops');

pimcore.plugin.coreshop.rules.conditions.shops = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {

    type : 'shops',

    getForm : function () {
        var me = this;
        var store = pimcore.globalmanager.get('coreshop_stores');

        var shops = {
            fieldLabel: t('coreshop_condition_shops'),
            typeAhead: true,
            listWidth: 100,
            width : 500,
            store: store,
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect : true,
            triggerAction: 'all',
            name:'shops',
            maxHeight : 400,
            delimiter : false,
            listeners: {
                beforerender: function () {
                    if (!store.isLoaded() && !store.isLoading())
                        store.load();

                    if (me.data && me.data.shops)
                        this.setValue(me.data.shops);
                }
            }
        };

        if (this.data && this.data.shops) {
            shops.value = this.data.shops;
        }

        shops = new Ext.ux.form.MultiSelect(shops);

        this.form = new Ext.form.Panel({
            items : [
                shops
            ]
        });

        return this.form;
    }
});
