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

pimcore.registerNS('coreshop.object.tags.coreShopSerializedData');
coreshop.object.tags.coreShopSerializedData = Class.create(pimcore.object.tags.abstract, {

    allowEmpty: false,

    initialize: function (data, fieldConfig) {
        this.data = data;
        this.fieldConfig = fieldConfig;
        this.fieldConfig.width = 350;
    },

    getLayoutEdit: function () {

        this.component = new Ext.panel.Panel({
            html: 'nothing to see here'
        });

        return this.component;
    }
});
