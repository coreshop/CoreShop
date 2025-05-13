/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('pimcore.object.tags.coreShopItemSelector');
pimcore.object.tags.coreShopItemSelector = Class.create(pimcore.object.tags.multiselect, {
    type: 'coreShopItemSelector',

     initialize: function (data, fieldConfig) {
        this.data = data;
        this.data_mapped = (data ? data : []).map(function(data) {
            return parseInt(data.id);
        });
        this.fieldConfig = fieldConfig;
    },

    getLayoutEdit: function() {
        Ext.require([
            'Ext.ux.form.ItemSelector'
        ]);

        this.options_store = new Ext.data.JsonStore({
            proxy: {
                type: 'ajax',
                url: Routing.generate('coreshop_dynamic_dropdown_options'),
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
            value: this.data_mapped
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
