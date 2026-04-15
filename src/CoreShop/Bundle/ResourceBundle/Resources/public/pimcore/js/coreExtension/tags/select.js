/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.object.tags.select');
coreshop.object.tags.select = Class.create(pimcore.object.tags.select, {

    allowEmpty: false,
    displayField: 'name',

    initialize: function (data, fieldConfig) {
        this.data = data;
        this.fieldConfig = fieldConfig;
        this.fieldConfig.width = 350;
    },

    getLayoutEdit: function () {
        // generate store
        var store = [];
        var validValues = [];

        if (pimcore.globalmanager.exists(this.storeName)) {
            store = pimcore.globalmanager.get(this.storeName);
        } else {
            throw this.storeName + ' should be added as valid store';
        }

        var comboBoxStore = new Ext.data.Store({
            proxy: store.proxy,
            reader: store.reader
        });

        if (store.isLoaded()) {
            comboBoxStore.add(store.getRange());

            if (this.fieldConfig.allowEmpty) {
                comboBoxStore.insert(0, {
                    name: t('empty'),
                    id: 0
                });
            }
        } else {
            comboBoxStore.load(function () {
                if (this.fieldConfig.allowEmpty) {
                    comboBoxStore.insert(0, {
                        name: t('empty'),
                        id: 0
                    });
                }
            }.bind(this));
        }

        var options = {
            name: this.fieldConfig.name,
            triggerAction: 'all',
            editable: false,
            typeAhead: false,
            forceSelection: true,
            fieldLabel: this.fieldConfig.title,
            store: comboBoxStore,
            componentCls: 'object_field',
            width: 250,
            labelWidth: 100,
            displayField: this.displayField,
            valueField: 'id',
            queryMode: 'local',
            value: this.data ? parseInt(this.data) : null,
            listeners: {
                beforerender: function () {
                    if (!store.isLoaded() && !store.isLoading())
                        store.load();
                },

                select: function (comp, record, index) {
                    if (comp.getValue() == 0 && this.fieldConfig.allowEmpty)
                        comp.setValue(null);
                }.bind(this)
            }
        };

        if (this.fieldConfig.labelWidth) {
            options.labelWidth = this.fieldConfig.labelWidth;
        }

        if (this.fieldConfig.width) {
            options.width = this.fieldConfig.width;
        }

        options.width += options.labelWidth;

        this.component = new Ext.form.ComboBox(options);

        return this.component;
    }
});
