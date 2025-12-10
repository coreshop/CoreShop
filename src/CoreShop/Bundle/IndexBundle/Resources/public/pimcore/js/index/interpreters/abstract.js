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

pimcore.registerNS('coreshop.index.interpreters');
pimcore.registerNS('coreshop.index.interpreters.abstract');

coreshop.index.interpreters.abstract = Class.create({

    getLayout: function (record, interpreterConfig) {
        return [];
    },

    getForm: function(record, interpreterConfig) {
        if (!this.form) {
            this.form = new Ext.form.FormPanel({
                defaults: {anchor: '90%'},
                layout: 'form',
                items: this.getLayout(record, interpreterConfig)
            });
        }

        return this.form;
    },

    isValid: function() {
        return this.getForm().getForm().isValid()
    },

    getInterpreterData: function() {
        return this.form.getForm().getFieldValues();
    },
});
