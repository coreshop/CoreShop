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

pimcore.registerNS('coreshop.order.order.create.customer');
coreshop.order.order.create.customer = Class.create(coreshop.resource.creation, {
    route: 'coreshop_admin_order_customer_creation',
    type: 'customer',

    getSettings: function() {
        return [
            this.getCustomerSettings(),
            this.getAddressSettings()
        ];
    },

    getCustomerSettings: function () {
        this.customerForm = Ext.create('Ext.form.FieldSet', {
            title: t('coreshop_customer_create_customer'),
            items: [{
                xtype: 'combobox',
                store: [['male', t('coreshop_gender_male')], ['female', t('coreshop_gender_female')]],
                name: this.options.prefix + 'gender',
                fieldLabel: t('coreshop_customer_create_gender'),
                allowBlank: false
            }, {
                xtype: 'textfield',
                name: this.options.prefix + 'firstname',
                fieldLabel: t('coreshop_customer_create_firstname'),
                allowBlank: false
            }, {
                xtype: 'textfield',
                name: this.options.prefix + 'lastname',
                fieldLabel: t('coreshop_customer_create_lastname'),
                allowBlank: false
            }, {
                xtype: 'textfield',
                vtype: 'email',
                name: this.options.prefix + 'email',
                fieldLabel: t('coreshop_customer_create_email'),
                allowBlank: false
            }]
        });

        return this.customerForm;
    },

    getAddressSettings: function () {
        return new coreshop.order.order.create.address({prefix: 'address.'}).getAddressSettings();
    },
});
