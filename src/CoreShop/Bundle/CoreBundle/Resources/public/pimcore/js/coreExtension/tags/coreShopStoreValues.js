/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.object.tags.coreShopStoreValues');
pimcore.object.tags.coreShopStoreValues = Class.create(pimcore.object.tags.abstract, {

    type: 'coreShopStoreValues',
    storeValuesBuilder: {},

    initialize: function (data, fieldConfig) {

        this.defaultValue = null;
        this.storeValuesBuilder = {};

        if ((typeof data === 'undefined' || data === null) && fieldConfig.defaultValue) {
            data = fieldConfig.defaultValue;
            this.defaultValue = data;
        }

        this.data = data;
        this.fieldConfig = fieldConfig;
        this.eventDispatcherKey = pimcore.eventDispatcher.registerTarget(this.eventDispatcherKey, this);
    },

    getGridColumnEditor: function (field) {
        return false;
    },

    getGridColumnFilter: function (field) {
        return false;
    },

    postSaveObject: function (object, task) {
        if (object.id === this.object.id && task === 'publish') {
            Ext.Object.each(this.storeValuesBuilder, function (storeId, builder) {
                builder.postSaveObject();
            });
        }
    },

    getLayoutEdit: function () {

        var tabPanel = new Ext.TabPanel({
                monitorResize: true,
                cls: 'object_field',
                activeTab: 0,
                height: 'auto',
                deferredRender: true,
                forceLayout: true,
                hideMode: 'offsets',
                enableTabScroll: true
            }),
            wrapperConfig = {
                border: true,
                layout: 'fit',
                style: 'margin-bottom: 10px'
            };

        this.fieldConfig.datatype = 'layout';
        this.fieldConfig.fieldtype = 'panel';

        if (this.fieldConfig.width) {
            wrapperConfig.width = this.fieldConfig.width;
        }

        if (this.fieldConfig.region) {
            wrapperConfig.region = this.fieldConfig.region;
        }

        if (this.fieldConfig.title) {
            wrapperConfig.title = this.fieldConfig.title;
        }

        if (this.context.containerType === 'fieldcollection') {
            this.context.subContainerType = 'localizedfield';
        } else {
            this.context.containerType = 'localizedfield';
        }

        if (this.fieldConfig.height) {
            tabPanel.setHeight(this.fieldConfig.height);
        }

        Ext.Object.each(pimcore.globalmanager.get('coreshop_stores').getRange(), function (index, store) {

            var data, valuesBuilder, formPanel = new Ext.Panel({
                xtype: 'panel',
                border: false,
                autoScroll: true,
                padding: '10px',
                deferredRender: true,
                hideMode: 'offsets',
                iconCls: 'coreshop_icon_store',
                title: store.get('name')
            });

            if (this.fieldConfig.labelWidth) {
                formPanel.labelWidth = this.fieldConfig.labelWidth;
            }

            data = this.data.hasOwnProperty(store.getId()) ? this.data[store.getId()] : null;
            valuesBuilder = new coreshop.product.storeValues.builder(this.fieldConfig, store, data);

            formPanel.add([valuesBuilder.getForm()]);
            tabPanel.add([formPanel]);

            this.storeValuesBuilder[store.getId()] = valuesBuilder;

        }.bind(this));

        this.tabPanel = tabPanel;
        this.component = new Ext.Panel(wrapperConfig);
        this.component.add([this.tabPanel]);
        this.component.on('destroy', function () {
            pimcore.eventDispatcher.unregisterTarget(this.eventDispatcherKey);
        }.bind(this));

        return this.component;
    },

    getLayoutShow: function () {
        this.component = this.getLayoutEdit(true);

        return this.component;
    },

    getValue: function () {
        var values = {};
        Ext.Object.each(this.storeValuesBuilder, function (storeId, builder) {
            values[storeId] = builder.getValues();
        });

        return values;
    },

    getName: function () {
        return this.fieldConfig.name;
    },

    isInvalidMandatory: function () {
        if (!this.isRendered() && (!empty(this.getInitialData() || this.getInitialData() === 0))) {
            return false;
        } else if (!this.isRendered()) {
            return true;
        }

        return this.getValue();
    },

    isDirty: function () {

        var dirty = false;

        if (this.defaultValue) {
            return true;
        }

        if (!this.isRendered()) {
            return false;
        }

        Ext.Object.each(this.storeValuesBuilder, function (index, builder) {
            if (builder.isDirty()) {
                dirty = true;
                return false;
            }
        });

        return dirty;
    }
});