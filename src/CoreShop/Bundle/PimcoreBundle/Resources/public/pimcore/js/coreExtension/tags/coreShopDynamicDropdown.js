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

pimcore.registerNS('pimcore.object.tags.coreShopDynamicDropdown');
pimcore.object.tags.coreShopDynamicDropdown = Class.create(pimcore.object.tags.select, {
    type: 'coreShopDynamicDropdown',

     initialize: function (data, fieldConfig) {
        this.data = data;
        this.data_mapped = data ? parseInt(data.id) : null;
        this.fieldConfig = fieldConfig;
    },

    getGridColumnEditor: function (field) {
        if (field.layout.noteditable) {
            return null;
        }
        this.options_store = new Ext.data.JsonStore({
            proxy: {
                type: 'ajax',
                url: Routing.generate('coreshop_dynamic_dropdown_options'),
                extraParams: {
                    folderName: field.layout.folderName,
                    methodName: field.layout.methodName,
                    className: field.layout.className,
                    recursive: field.layout.recursive,
                    current_language: pimcore.settings.language,
                    sortBy: field.layout.sortBy
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
                load: function (store, records, success, operation) {

                }.bind(this)
            },
            autoLoad: true
        });

        var options = {
            store: this.options_store,
            triggerAction: 'all',
            editable: false,
            mode: 'local',
            valueField: 'value',
            displayField: 'key',
            autoComplete: false,
            forceSelection: true,
            selectOnFocus: true,
        };

        return new Ext.form.ComboBox(options);
    },

    getGridColumnConfig: function (field) {
        var renderer = function (key, value, metaData, record) {

            this.applyPermissionStyle(key, value, metaData, record);

            if (record.data.inheritedFields[key] && record.data.inheritedFields[key].inherited === true) {
                try {
                    metaData.tdCls += ' grid_value_inherited';
                } catch (e) {
                    console.log(e);
                }
            }

            return value;

        }.bind(this, field.key);

        return {
            header: ts(field.label), sortable: true, dataIndex: field.key, renderer: renderer,
            editor: this.getGridColumnEditor(field)
        };
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
                load: function (store, records, success, operation) {
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
            editable: true,
            typeAhead: true,
            forceSelection: true,
            selectOnFocus: true,
            fieldLabel: this.fieldConfig.title,
            store: this.options_store,
            itemCls: 'object_field',
            width: 300,
            displayField: 'key',
            valueField: 'value',
            queryMode: 'local',
            autoSelect: false,
            autoLoadOnValue: true,
            value: this.data_mapped,
            plugins: ['clearbutton'],
            listConfig: {
                getInnerTpl: function (displayField) {
                    return '<tpl for="."><tpl if="published == true">{key}<tpl else><div class="x-combo-item-disabled x-item-disabled">{key}</div></tpl></tpl>';
                }
            }
        };

        if (this.fieldConfig.width) {
            options.width = this.fieldConfig.width;
        }


        this.component = new Ext.form.ComboBox(options);

        if (!this.fieldConfig.onlyPublished) {
            this.component.addListener('beforeselect', function (combo, record, index, e) {
                if (!record.data.published) {
                    return false;
                }
            });
        }

        return this.component;
    }
});
