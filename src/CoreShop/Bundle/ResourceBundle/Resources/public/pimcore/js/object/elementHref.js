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

pimcore.registerNS('coreshop.object.elementHref');
coreshop.object.elementHref = Class.create(pimcore.object.tags.manyToOneRelation, {
    getLayoutEdit: function ($super) {
        var me = this,
            element = $super();

        if (this.data) {
            if (!this.data.path) {
                this.component.setValue(this.data.id);
            }
        }

        this.component.setReadOnly(true);

        this.component.getModelData = function (includeEmptyText, /*private*/
                                                isSubmitting) {
            var data = null;
            // Note that we need to check if this operation is being called from a Submit action because displayfields aren't
            // to be submitted,  but they can call this to get their model data.
            if (!this.disabled && (this.submitValue || !isSubmitting)) {
                data = {};
                data[this.getFieldIdentifier()] = me.getValue();
            }
            return data;
        };

        return element;
    },

    requestNicePathData: function () {
        if (this.data.id) {
            coreshop.helpers.requestNicePathData([this.data], function (responseData) {
                if (typeof responseData[this.data.id] !== "undefined") {
                    this.component.setValue(responseData[this.data.id]);
                }
            }.bind(this));
        }
    },

    getValue: function () {
        return this.data.id;
    }
});
