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

pimcore.registerNS("pimcore.object.tags.coreShopStorePrice");
pimcore.object.tags.coreShopStorePrice = Class.create(pimcore.object.tags.abstract, {

    type: "coreShopStorePrice",
    storeFields: {},

    initialize: function (data, fieldConfig) {
        this.defaultValue = null;
        this.storeFields = {};

        if ((typeof data === "undefined" || data === null) && fieldConfig.defaultValue) {
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

    postSaveObject: function(object, task)
    {
        if (object.id === this.object.id && task === "publish")
        {
            Ext.Object.each(this.storeFields, function(key, value) {
                value.resetOriginalValue();
            });
        }
    },

    getLayoutEdit: function () {
        this.fieldConfig.datatype = "layout";
        this.fieldConfig.fieldtype = "panel";

        var wrapperConfig = {
            border: false,
            layout: "fit"
        };

        if (this.fieldConfig.width) {
            wrapperConfig.width = this.fieldConfig.width;
        }

        if (this.fieldConfig.region) {
            wrapperConfig.region = this.fieldConfig.region;
        }

        if (this.fieldConfig.title) {
            wrapperConfig.title = this.fieldConfig.title;
        }

        if (this.context.containerType === "fieldcollection") {
            this.context.subContainerType = "localizedfield";
        } else {
            this.context.containerType = "localizedfield";
        }

        var stores = pimcore.globalmanager.get('coreshop_stores').getRange();

        var panelConf = {
            monitorResize: true,
            cls: "object_field",
            activeTab: 0,
            height: "auto",
            items: [],
            deferredRender: true,
            forceLayout: true,
            hideMode: "offsets",
            enableTabScroll: true
        };

        if (this.fieldConfig.height) {
            panelConf.height = this.fieldConfig.height;
            panelConf.autoHeight = false;
        }

        for (var i = 0; i < stores.length; i++) {
            var store = stores[i],
                storeData = this.data.hasOwnProperty(store.getId()) ? this.data[store.getId()] : false;

            var input = {
                xtype: 'numberfield',
                fieldLabel: this.fieldConfig.title,
                name: this.fieldConfig.name,
                componentCls: 'object_field',
                coreshopStore: store,
                labelWidth: 250,
                value: this.defaultValue,
                listeners: {
                    afterrender: function(comp, value) {
                        coreshop.broker.fireEvent('core.store_price.price_initialize', value, comp.coreshopStore, this.object);

                    }
                    change: function(comp, value) {
                        coreshop.broker.fireEvent('core.store_price.price_change', value, comp.coreshopStore, this.object);
                    }.bind(this)
                }
            };

            if (storeData) {
                input.value = storeData.price;
                input.fieldLabel = input.fieldLabel + " (" + storeData.currencySymbol + ")";
            }

            if (this.fieldConfig.width) {
                input.width = this.fieldConfig.width;
            } else {
                input.width = 350;
            }

            input.width += input.labelWidth;

            if (is_numeric(this.fieldConfig["minValue"])) {
                input.minValue = this.fieldConfig.minValue;
            }

            if (is_numeric(this.fieldConfig["maxValue"])) {
                input.maxValue = this.fieldConfig.maxValue;
            }

            this.storeFields[store.getId()] = Ext.create(input);

            var item = {
                xtype: "panel",
                border: false,
                autoScroll: true,
                padding: "10px",
                deferredRender: true,
                hideMode: "offsets",
                items: this.storeFields[store.getId()]
            };

            item.iconCls = "coreshop_icon_store";
            item.title = store.get('name');

            if (this.fieldConfig.labelWidth) {
                item.labelWidth = this.fieldConfig.labelWidth;
            }

            panelConf.items.push(item);
        }

        this.tabPanel = new Ext.TabPanel(panelConf);

        wrapperConfig.items = [this.tabPanel];

        wrapperConfig.border = true;
        wrapperConfig.style = "margin-bottom: 10px";

        this.component = new Ext.Panel(wrapperConfig);
        this.component.updateLayout();

        this.component.on("destroy", function() {
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

        Ext.Object.each(this.storeFields, function (key, input) {
            values[key] = input.getValue();
        });

        return values;
    },

    getName: function () {
        return this.fieldConfig.name;
    },

    isInvalidMandatory: function () {
        if (!this.isRendered() && (!empty(this.getInitialData() || this.getInitialData() === 0) )) {
            return false;
        } else if (!this.isRendered()) {
            return true;
        }

        return this.getValue();
    },

    isDirty: function () {
        if (this.defaultValue) {
            return true;
        }

        if (!this.isRendered()) {
            return false;
        }

        var currentKey, currentInput;
        var keys = Object.keys(this.storeFields);

        for (var i = 0; i < keys.length; i++)
        {
            currentKey = keys[i];
            currentInput = this.storeFields[currentKey];

            if (currentInput.isDirty()) {
                return true;
            }
        }

        return false;
    }
});