/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.object.tags.coreShopDynamicDropdownMultiple');
pimcore.object.tags.coreShopDynamicDropdownMultiple = Class.create(pimcore.object.tags.multiselect, {
    type: 'coreShopDynamicDropdownMultiple',

     initialize: function (data, fieldConfig) {
        this.data = data;
        this.data_mapped = (data ? data : []).map(function(data) {
            return parseInt(data.id);
        });
        this.fieldConfig = fieldConfig;
    },

    getLayoutEdit: function () {
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
            triggerAction: 'all',
            editable: false,
            fieldLabel: this.fieldConfig.title,
            store: this.options_store,
            componentCls: 'object_field',
            height: 100,
            displayField: 'key',
            valueField: 'value',
            labelWidth: this.fieldConfig.labelWidth ? this.fieldConfig.labelWidth : 100,
            autoLoadOnValue: true,
            value: this.data_mapped
        };

        options.width = 300;
        if (this.fieldConfig.width) {
            options.width = this.fieldConfig.width;
        }

        options.width += options.labelWidth;

        if (this.fieldConfig.height) {
            options.height = this.fieldConfig.height;
        }

        this.component = Ext.create('Ext.ux.form.MultiSelect', options);

        return this.component;
    },

    getGridColumnEditor:function (field) {
        return null;
    },

    getGridColumnFilter:function (field) {
        return null;
    }


});
