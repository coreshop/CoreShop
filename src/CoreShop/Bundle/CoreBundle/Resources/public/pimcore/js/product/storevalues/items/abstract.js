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

pimcore.registerNS('coreshop.product.storeValues.items');
pimcore.registerNS('coreshop.product.storeValues.items.abstract');

coreshop.product.storeValues.items.abstract = Class.create({

    builder: null,

    initialize: function (builder) {
        this.builder = builder;
    },

    getForm: function () {
        // needs to be modified by 3rd party.
        return [];
    },

    onUnitDefinitionsReadyOrChange: function (data) {
        // keep it for 3rd party modifiers.
    },

    getDataValue: function (key) {

        var data, values;

        data = this.builder.data !== null && Ext.isObject(this.builder.data) ? this.builder.data : null;
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
    }
});
