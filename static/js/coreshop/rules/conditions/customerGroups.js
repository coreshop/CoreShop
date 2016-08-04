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

pimcore.registerNS('pimcore.plugin.coreshop.rules.conditions.customerGroups');

pimcore.plugin.coreshop.rules.conditions.customerGroups = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {

    type : 'customerGroups',

    getForm : function () {
        var me = this;
        var store = pimcore.globalmanager.get('coreshop_customergroups');

        var customerGroups = {
            fieldLabel: t('coreshop_condition_customerGroups'),
            typeAhead: true,
            listWidth: 100,
            width : 500,
            store: store,
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect : true,
            triggerAction: 'all',
            name:'customerGroups',
            maxHeight : 400,
            listeners: {
                beforerender: function () {
                    if (!store.isLoaded() && !store.isLoading())
                        store.load();

                    if (me.data && me.data.customerGroups)
                        this.setValue(me.data.customerGroups);
                }
            }
        };

        customerGroups = new Ext.ux.form.MultiSelect(customerGroups);

        if (this.data && this.data.customerGroups) {
            customerGroups.value = this.data.customerGroups;
        }

        this.form = new Ext.form.FieldSet({
            items : [
                customerGroups
            ]
        });

        return this.form;
    }
});
