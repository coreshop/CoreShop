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

pimcore.registerNS('pimcore.object.tags.coreShopProductUnitDefinitions');
pimcore.object.tags.coreShopProductUnitDefinitions = Class.create(pimcore.object.tags.abstract, {

    type: 'coreShopProductUnitDefinitions',
    unitBuilder: {},

    initialize: function (data, fieldConfig) {

        this.defaultValue = null;
        this.unitBuilder = {};

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

        var fieldName = this.getName();

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
        this.unitBuilder.postSaveObject(object, refreshedData, task, fieldName);
    },

    getLayoutEdit: function () {

        var unitBuilder,
            wrapperConfig = {
                border: true,
                layout: 'fit',
                style: 'margin: 10px 0;',
                collapsible: true
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

        unitBuilder = new coreshop.product.unit.builder(this.fieldConfig, this.data, this.object.id);

        this.unitBuilder = unitBuilder;
        this.component = new Ext.Panel(wrapperConfig);
        this.component.add([unitBuilder.getForm()]);

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
        return this.unitBuilder.getValues();
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

        if (this.defaultValue) {
            return true;
        }

        if (!this.isRendered()) {
            return false;
        }

        return this.unitBuilder.isDirty();
    }
});