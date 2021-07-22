/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.object.tags.coreShopStoreValues');
pimcore.object.tags.coreShopStoreValues = Class.create(pimcore.object.tags.abstract, {

    type: 'coreShopStoreValues',
    storeValuesBuilder: {},
    productUnitDefinitionsStore: null,

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

    setObject: function (object) {
        this.object = object;
        // we need to define the unit definition store on tag layer
        // otherwise each store builder would refresh a single request
        this.productUnitDefinitionsStore = new Ext.data.Store({
            autoDestroy: true,
            proxy: {
                type: 'ajax',
                url: Routing.generate('coreshop_product_unit_definitions_productAdditionalUnitDefinitionsList'),
                extraParams: {
                    productId: this.object.id
                },
                actionMethods: {
                    read: 'GET'
                },
                reader: {
                    type: 'json'
                }
            },
            fields: ['id', 'unit']
        }).load();

        this.productUnitDefinitionsStore.on('datachanged', function () {
            Ext.Object.each(this.storeValuesBuilder, function (storeId, builder) {
                builder.onUnitDefinitionsReadyOrChange();
            });
        }.bind(this));

        coreshop.broker.addListener('pimcore.object.tags.coreShopProductUnitDefinitions.change', this.onUnitDefinitionsChange, this);

    },

    getGridColumnEditor: function (field) {
        return false;
    },

    getGridColumnFilter: function (field) {
        return false;
    },

    postSaveObject: function (object, task) {

        var fieldName = this.getName();

        if (object.id !== this.object.id) {
            return;
        }

        if (this.isDirty()) {
            this.reloadStoreValuesData(object, task, fieldName);
        }
    },

    reloadStoreValuesData: function (object, task, fieldName) {
        this.component.setLoading(true);
        Ext.Ajax.request({
            url: Routing.generate('pimcore_admin_dataobject_dataobject_get'),
            params: {id: object.id},
            ignoreErrors: true,
            success: function (response) {

                // maybe object is already gone due manual reload
                if(this.component.destroyed === true) {
                    return;
                }

                this.dirty = false;

                var refreshedObject = null,
                    refreshedObjectData = null;
                try {
                    refreshedObject = Ext.decode(response.responseText);
                    if (!refreshedObject.hasOwnProperty('data') || !refreshedObject.data.hasOwnProperty(fieldName)) {
                        this.component.setLoading(false);
                        return;
                    }
                    refreshedObjectData = refreshedObject.data[fieldName];
                } catch (e) {
                    console.log(e);
                }

                this.component.setLoading(false);
                if (refreshedObjectData !== null) {
                    this.dispatchPostSaveToBuilders(object, refreshedObjectData, task, fieldName);
                }
            }.bind(this),
            failure: function () {
                this.component.setLoading(false);
            }.bind(this),
        });
    },

    dispatchPostSaveToBuilders: function (object, refreshedData, task, fieldName) {
        Ext.Object.each(this.storeValuesBuilder, function (storeId, builder) {
            var refreshedStoreData = {};
            if (Ext.isObject(refreshedData) && refreshedData.hasOwnProperty(storeId)) {
                refreshedStoreData = refreshedData[storeId];
            }
            builder.postSaveObject(object, refreshedStoreData, task, fieldName);
        });
    },

    onUnitDefinitionsChange: function (data) {

        if (data.objectId !== this.object.id) {
            return;
        }

        Ext.Object.each(this.storeValuesBuilder, function (storeId, builder) {
            builder.onUnitDefinitionsReadyOrChange(data);
        });

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
                title: store.get('name'),
                items: []
            });

            if (this.fieldConfig.labelWidth) {
                formPanel.labelWidth = this.fieldConfig.labelWidth;
            }

            data = this.data.hasOwnProperty(store.getId()) ? this.data[store.getId()] : null;
            valuesBuilder = new coreshop.product.storeValues.builder(this.fieldConfig, store, data, this.productUnitDefinitionsStore, this.object.id);

            if (data && data.hasOwnProperty('inherited') && !data.inherited && data.inheritable) {
                formPanel.add({
                    xtype: 'button',
                    text: t('coreshop_restore_inheritance'),
                    iconCls: 'pimcore_icon_delete',
                    handler: function() {
                        Ext.Msg.confirm(t('coreshop_restore_inheritance'), t('coreshop_restore_inheritance_message'), function (btn) {
                            if (btn === 'yes') {
                                this.component.setLoading(true);

                                Ext.Ajax.request({
                                    url: Routing.generate('coreshop_product_removeStoreValues'),
                                    method: 'post',
                                    params: {id: this.object.id, storeValuesId: data.values.id},
                                    ignoreErrors: true,
                                    success: function (response) {
                                        // maybe object is already gone due manual reload
                                        if(this.component.destroyed === true) {
                                            return;
                                        }

                                        this.component.setLoading(false);

                                        pimcore.globalmanager.get('object_' + this.object.id).reload();

                                    }.bind(this),
                                    failure: function () {
                                        this.component.setLoading(false);
                                    }.bind(this),
                                });
                            }
                        }.bind(this));
                    }.bind(this)
                });
            }

            formPanel.add([valuesBuilder.getForm()]);
            tabPanel.add([formPanel]);

            this.storeValuesBuilder[store.getId()] = valuesBuilder;

        }.bind(this));

        tabPanel.setActiveItem(0);

        this.tabPanel = tabPanel;
        this.component = new Ext.Panel(wrapperConfig);
        this.component.add([this.tabPanel]);
        this.component.on('destroy', function () {
            pimcore.eventDispatcher.unregisterTarget(this.eventDispatcherKey);
            coreshop.broker.removeListener('pimcore.object.tags.coreShopProductUnitDefinitions.change', this.onUnitDefinitionsChange);
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
