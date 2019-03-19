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

pimcore.registerNS('coreshop.product.storeValues.builder');
coreshop.product.storeValues.builder = Class.create({

    data: null,
    objectId: null,
    store: null,
    fieldConfig: null,
    form: null,
    dirty: false,
    itemBlocks: [],
    productUnitDefinitionsStore: null,

    initialize: function (fieldConfig, store, data, productUnitDefinitionsStore, objectId) {

        this.fieldConfig = fieldConfig;
        this.store = store;
        this.data = data;
        this.productUnitDefinitionsStore = productUnitDefinitionsStore;
        this.objectId = objectId;
        this.dirty = false;
        this.itemBlocks = [];

        this.setupForm();

    },

    setupForm: function () {

        this.form = new Ext.form.Panel({
            closable: false
        });

        this.getItems();
    },

    getItems: function () {

        var items = Object.keys(coreshop.product.storeValues.items);

        Ext.Array.each(items, function (item) {
            var itemForm;
            if (item !== 'abstract') {
                itemForm = new coreshop.product.storeValues.items[item](this);
                this.form.add(itemForm.getForm());
                this.itemBlocks.push(itemForm);
            }
        }.bind(this));

    },

    getForm: function () {
        return this.form;
    },

    getDataValue: function (key) {

        var data, values;

        data = this.data !== null && Ext.isObject(this.data) ? this.data : null;
        if (data === null) {
            return null;
        }

        values = data.values !== null && Ext.isObject(data.values) ? data.values : null;
        if (values === null) {
            return null;
        }

        if (values.hasOwnProperty(key)) {
            return values[key];
        }

        return null;
    },

    onUnitDefinitionsReadyOrChange: function (data) {

        Ext.Array.each(this.itemBlocks, function (item) {
            item.onUnitDefinitionsReadyOrChange(data);
        }.bind(this));
    },

    postSaveObject: function (object, refreshedData) {

        if (Ext.isObject(refreshedData) && Ext.isObject(refreshedData.values)) {
            this.data.values = refreshedData.values;
        }

        this.dirty = false;

        this.form.getForm().getFields().each(function (item) {
            item.resetOriginalValue();
        });
    },

    isDirty: function () {

        if (this.dirty === true) {
            return true;
        }

        if (this.form.getForm().isDirty()) {
            return true;
        }

        return false;
    },

    getValues: function () {
        var formValues = this.form.getForm().getFieldValues();
        if (this.getDataValue('id') !== null) {
            formValues['id'] = this.getDataValue('id');
        }

        return formValues;
    }
});