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

pimcore.registerNS('coreshop.object.tags.multiselect');
coreshop.object.tags.multiselect = Class.create(pimcore.object.tags.multiselect, {
    displayField: 'name',

    getLayoutEdit: function () {

        // generate store
        var store = [];

        if (pimcore.globalmanager.exists(this.storeName)) {
            store = pimcore.globalmanager.get(this.storeName);
        } else {
            console.log(this.storeName + ' should be added as valid store');
        }

        var options = {
            name: this.fieldConfig.name,
            triggerAction: 'all',
            editable: false,
            fieldLabel: this.fieldConfig.title,
            store: store,
            itemCls: 'object_field',
            maxHeight: 400,
            queryMode: 'local',
            displayField: this.displayField,
            valueField: 'id',
            listeners: {
                beforerender: function () {
                    if (!store.isLoaded() && !store.isLoading())
                        store.load();
                }
            }
        };

        if (this.fieldConfig.width) {
            options.width = this.fieldConfig.width;
        }

        if (this.fieldConfig.height) {
            options.height = this.fieldConfig.height;
        }

        if (typeof this.data == 'string' || typeof this.data == 'number') {
            options.value = this.data;
        }

        this.component = new Ext.ux.form.MultiSelect(options);

        return this.component;
    }

});
