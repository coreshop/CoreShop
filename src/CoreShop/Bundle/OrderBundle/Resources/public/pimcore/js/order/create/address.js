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

pimcore.registerNS('coreshop.order.order.create.address');
coreshop.order.order.create.address = Class.create(coreshop.resource.creation, {
    route: 'coreshop_admin_order_address_creation',
    type: 'address',

    initialize: function (options, callback) {
        coreshop.resource.creation.prototype.initialize.apply(this, arguments);

        this.options = options;
        this.mode = options?.params?.mode || null;
    },


    getSettings: function () {
        const mode = this.mode || this.options?.params?.mode || null;

        if (mode === 'primary') {
            return [this.getAddressSettings()];
        }

        return [this.getAddressSettings()];
    },


    getAddressSettings: function () {
        this.addressForm = Ext.create('Ext.form.FieldSet', {
            title: t('coreshop_address_create'),
            items: [{
                xtype: 'coreshop.countrySalutation',
                country: {
                    name: this.options.prefix + 'country',
                    fieldLabel: t('coreshop_address_create_country'),
                },
                salutation: {
                    name: this.options.prefix + 'salutation',
                    fieldLabel: t('coreshop_address_create_salutation'),
                },
            }, {
                xtype: 'textfield',
                name: this.options.prefix + 'firstname',
                fieldLabel: t('coreshop_address_create_firstname'),
                allowBlank: false
            }, {
                xtype: 'textfield',
                name: this.options.prefix + 'lastname',
                fieldLabel: t('coreshop_address_create_lastname'),
                allowBlank: false
            }, {
                xtype: 'textfield',
                name: this.options.prefix + 'street',
                fieldLabel: t('coreshop_address_create_street'),
                allowBlank: false
            }, {
                xtype: 'textfield',
                name: this.options.prefix + 'number',
                fieldLabel: t('coreshop_address_create_number'),
                allowBlank: false
            }, {
                xtype: 'textfield',
                name: this.options.prefix + 'postcode',
                fieldLabel: t('coreshop_address_create_postcode'),
                allowBlank: false
            }, {
                xtype: 'textfield',
                name: this.options.prefix + 'city',
                fieldLabel: t('coreshop_address_create_city'),
                allowBlank: false
            }, {
                xtype: 'coreshop.state',
                name: this.options.prefix + 'state',
                allowBlank: false
            }, {
                xtype: 'textfield',
                name: this.options.prefix + 'phoneNumber',
                fieldLabel: t('coreshop_address_create_phone_number')
            }]
        });

        return this.addressForm;
    }
});
