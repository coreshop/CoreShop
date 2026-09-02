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
            labelWidth: this.fieldConfig.labelWidth || 100,
            listeners: {
                beforerender: function () {
                    if (!store.isLoaded() && !store.isLoading()) {
                        store.load();
                    }
                },
                change: function (multiselect, newValue, oldValue) {
                    if (this.fieldConfig.maxItems && multiselect.getValue().length > this.fieldConfig.maxItems) {
                        // we need to set a timeout so setValue is applied when change event is totally finished
                        // without this, multiselect won't be updated visually with oldValue (but internal value will be oldValue)
                        setTimeout(function(multiselect, oldValue){
                            multiselect.setValue(oldValue);
                        }, 100, multiselect, oldValue);

                        Ext.Msg.alert(t('error'), t('limit_reached'));
                    }

                    return true;
                }.bind(this),
            }
        };

        if (this.fieldConfig.width) {
            options.width = this.fieldConfig.width;
        }

        if (this.fieldConfig.height) {
            options.height = this.fieldConfig.height;
        }

        if (this.fieldConfig.labelAlign) {
            options.labelAlign = this.fieldConfig.labelAlign;
        }

        if (!this.fieldConfig.labelAlign || 'left' === this.fieldConfig.labelAlign) {
            options.width = this.sumWidths(options.width, options.labelWidth);
        }

        if (typeof this.data == 'string' || typeof this.data == 'number') {
            options.value = this.data;
        }

        if (this.fieldConfig.renderType === 'tags') {
            options.queryMode = 'local';
            options.editable = true;
            options.anyMatch = true;
            options.plugins = 'dragdroptag';

            this.component = new Ext.form.field.Tag(options);
        } else {
            this.component = new Ext.ux.form.MultiSelect(options);
        }

        return this.component;
    }

});
