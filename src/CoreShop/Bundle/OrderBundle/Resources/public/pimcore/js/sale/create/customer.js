/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.sale.create.customer');
coreshop.order.sale.create.customer = Class.create(coreshop.resource.creation, {
    url: '/admin/coreshop/order/customer/create',
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
        return new coreshop.order.sale.create.address({prefix: 'address.'}).getAddressSettings();
    },
});
