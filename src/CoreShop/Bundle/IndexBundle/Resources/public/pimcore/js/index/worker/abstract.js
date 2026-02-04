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

pimcore.registerNS('coreshop.index.worker');
pimcore.registerNS('coreshop.index.worker.abstract');

coreshop.index.worker.abstract = Class.create({
    parent: null,

    initialize: function (parent) {
        this.parent = parent;
    },

    getForm: function (configuration) {
        return Ext.form.Panel({
            items: this.getFields(configuration)
        });
    },

    getFields: function (configuration) {
        return [];
    }
});
