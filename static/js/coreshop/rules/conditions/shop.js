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

pimcore.registerNS('pimcore.plugin.coreshop.rules.conditions.shop');

pimcore.plugin.coreshop.rules.conditions.shop = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {

    type : 'shop',

    getForm : function () {
        var me = this;
        var store = pimcore.globalmanager.get('coreshop_shops');

        var shop = {
            xtype: 'combo',
            fieldLabel: t('coreshop_condition_shop'),
            typeAhead: true,
            listWidth: 100,
            width : 500,
            store: store,
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            name:'shop',
            listeners: {
                beforerender: function () {
                    if (!store.isLoaded() && !store.isLoading())
                        store.load();

                    if (me.data && me.data.shop)
                        this.setValue(me.data.shop);
                }
            }
        };

        if (this.data && this.data.shop) {
            shop.value = this.data.shop;
        }

        this.form = new Ext.form.FieldSet({
            items : [
                shop
            ]
        });

        return this.form;
    }
});
