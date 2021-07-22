/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.object.tags.coreShopProductUnitDefinitions');
pimcore.object.tags.coreShopProductUnitDefinitions = Class.create(pimcore.object.tags.abstract, {

    type: 'coreShopProductUnitDefinitions',
    unitBuilder: {},
    unitStore: null,
    unitStoreLoaded: false,

    initialize: function (data, fieldConfig) {
        this.data = data;
        this.unitBuilder = {};
        this.unitStore = pimcore.globalmanager.get('coreshop_product_units');
        this.fieldConfig = fieldConfig;
        this.eventDispatcherKey = pimcore.eventDispatcher.registerTarget(this.eventDispatcherKey, this);
    },

    getGridColumnEditor: function (field) {
        return false;
    },

    getGridColumnFilter: function (field) {
        return false;
    },

    getLayoutShow: function () {
        this.component = this.getLayoutEdit();
        return this.component;
    },

    getLayoutEdit: function () {

        var wrapperConfig = {
            border: true,
            layout: 'fit',
            style: 'margin: 10px 0;',
            collapsible: true,
            collapsed: true
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

        this.component = new Ext.Panel(wrapperConfig);
        this.component.on('destroy', function () {
            pimcore.eventDispatcher.unregisterTarget(this.eventDispatcherKey);
        }.bind(this));

        this.initiateUnitStoreField();

        return this.component;
    },

    initiateUnitStoreField: function () {
        if (this.unitStore.isLoaded()) {
            this.setupUnitStoreField();
        } else {
            this.unitStore.load(function (store) {
                this.setupUnitStoreField();
            }.bind(this));
        }
    },

    setupUnitStoreField: function () {

        // do not show extra unit fields if no units are available.
        if (this.unitStore.getRange().length === 0) {
            this.component.add([{
                'xtype': 'label',
                'style': 'margin:5px; font-style:italic;',
                'html': t('coreshop_product_unit_no_units_available')
            }]);
            return;
        }

        this.unitStoreLoaded = true;

        this.component.expand();

        this.unitBuilder = new coreshop.product.unit.builder(this.unitStore, this.fieldConfig, this.data, this.object.id);
        this.component.add([this.unitBuilder.getForm()]);

    },

    getValue: function () {
        var values = this.unitBuilder.getValues();

        if (this.data !== null && is_numeric(this.data.id)) {
            values['id'] = this.data.id;
        }

        return values;
    },

    getName: function () {
        return this.fieldConfig.name;
    },

    postSaveObject: function (object, task) {

        var fieldName = this.getName();

        if (this.unitStoreLoaded === false) {
            return;
        }

        if (object.id !== this.object.id) {
            return;
        }

        if (this.isDirty()) {
            this.reloadUnitValuesData(object, task, fieldName);
        }
    },

    reloadUnitValuesData: function (object, task, fieldName) {
        this.component.setLoading(true);
        Ext.Ajax.request({
            url: '/admin/object/get',
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
                    this.data = refreshedObjectData;
                    this.dispatchPostSaveToBuilders(object, refreshedObjectData, task, fieldName);
                }
            }.bind(this),
            failure: function () {
                this.component.setLoading(false);
            }.bind(this),
        });
    },

    dispatchPostSaveToBuilders: function (object, refreshedData, task, fieldName) {
        this.unitBuilder.postSaveObject(object, refreshedData, task, fieldName);
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

        if (this.unitStoreLoaded === false) {
            return false;
        }

        if (!this.isRendered()) {
            return false;
        }

        if (this.data === null || !is_numeric(this.data.id)) {
            return true;
        }

        return this.unitBuilder.isDirty();
    }
});
