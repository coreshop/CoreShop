/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

Ext.Loader.setPath('Ext.ux', '/pimcore/static6/js/lib/ext/ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

pimcore.registerNS('pimcore.object.tags.coreShopItemSelector');
pimcore.object.tags.coreShopItemSelector = Class.create(pimcore.object.tags.multiselect, {
    delimiter:',',
    type: 'coreShopItemSelector',

    getLayoutEdit: function() {
        this.options_store = new Ext.data.JsonStore({
            proxy: {
                type: 'ajax',
                url: '/admin/coreshop/dynamic-dropdown/options',
                extraParams: {
                    folderName: this.fieldConfig.folderName,
                    methodName: this.fieldConfig.methodName,
                    className: this.fieldConfig.className,
                    recursive: this.fieldConfig.recursive,
                    current_language: pimcore.settings.language,
                    sortBy: this.fieldConfig.sortBy
                },
                reader: {
                    type: 'json',
                    rootProperty: 'options',
                    successProperty: 'success',
                    messageProperty: 'message'
                }
            },
            fields: ['key', 'value'],
            listeners: {
                load: function(store, records, success, operation) {
                    if (!success) {
                        pimcore.helpers.showNotification(t('error'), t('coreshop_dynamic_dropdown_error_loading_options'), 'error', operation.getError());
                    }
                }.bind(this)
            },
            autoLoad: true
        });

        var options = {
            name: this.fieldConfig.name,
            displayField: 'key',
            valueField: 'value',
            fieldLabel: this.fieldConfig.title,
            store: this.options_store,
            fromTitle: t('coreshop_dynamic_dropdown_itemselector_available'),
            toTitle: t('coreshop_dynamic_dropdown_itemselector_selected'),
            width: 600,
            value: this.data
        };

        if (this.fieldConfig.width) {
            options.width = this.fieldConfig.width;
        }

        this.component = new Ext.ux.form.ItemSelector(options);
        return this.component;


    },

    getGridColumnEditor:function (field) {
        return null;
    },

    getGridColumnFilter:function (field) {
        return null;
    }
});