/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */
pimcore.registerNS('coreshop.core.customer.customerToCompanyTransformer');
coreshop.core.customer.customerToCompanyTransformer = Class.create(coreshop.core.customer.abstractCustomerModifier, {
    customerId: null,
    customerData: null,
    initialize: function () {
        this.getSelector('customer');
    },

    processNextStep: function (data) {

        this.customerId = data.id;
        this.customerData = data;

        this.validateAssignment({customerId: this.customerId})
    },

    buildAssignerLayout: function (data) {

        var busy = false,
            duplicateNameCheckStore,
            duplicateNamesGrid;

        duplicateNameCheckStore = new Ext.data.Store({
            autoDestroy: true,
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: Routing.generate('coreshop_admin_customer_company_name_duplication_check'),
                reader: {
                    type: 'json',
                    rootProperty: 'list'
                }
            },
            fields: ['id', 'name', 'path']
        });

        duplicateNamesGrid = {
            xtype: 'gridpanel',
            title: t('coreshop_customer_transformer_assignment_new_form_maybe_duplicates_title'),
            viewConfig: {
                enableTextSelection: true
            },
            hidden: true,
            autocomplete: 'none',
            autoCreate: {autocomplete: 'none'},
            store: duplicateNameCheckStore,
            columns: [
                {
                    text: t('id'),
                    dataIndex: 'id',
                    flex: 1
                },
                {
                    text: t('name'),
                    dataIndex: 'name',
                    flex: 2
                },
                {
                    text: t('path'),
                    dataIndex: 'path',
                    flex: 2
                }
            ]
        };

        var window = new Ext.window.Window({
            width: 900,
            height: 600,
            resizeable: false,
            modal: true,
            layout: 'fit',
            title: t('coreshop_customer_transformer_assignment_form_title'),
            items: [{
                xtype: 'form',
                bodyStyle: 'padding:20px 5px 20px 5px;',
                border: false,
                autoScroll: true,
                forceLayout: true,
                fieldDefaults: {
                    labelWidth: 150
                },
                buttons: [
                    {
                        text: t('coreshop_customer_transformer_assignment_new_form_button'),
                        handler: this.submitAssignment.bind(this),
                        iconCls: 'pimcore_icon_apply'
                    }
                ],
                items: [
                    {
                        xtype: 'label',
                        style: 'background:#e6e6e6; padding:10px; margin: 0 0 10px 0; display: block;',
                        html: t('coreshop_customer_transformer_assignment_new_form_description')
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('coreshop_customer_transformer_assignment_form_customer_id'),
                        disabled: true,
                        value: this.customerId
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('coreshop_customer_transformer_assignment_form_customer_name'),
                        disabled: true,
                        value: this.customerData.name
                    },
                    {
                        xtype: 'fieldset',
                        title: t('coreshop_customer_transformer_assignment_form_new_company_title'),
                        autoHeight: true,
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: t('coreshop_customer_transformer_assignment_form_company_name'),
                                disabled: false,
                                allowBlank: false,
                                width: 500,
                                autoComplete: 'none',
                                name: 'new_company_name',
                                value: '',
                                listeners: {
                                    afterrender: function (cmp) {
                                        cmp.inputEl.set({autocomplete: 'none'});
                                    },
                                    change: function (el) {

                                        var gridPanel = el.up('fieldset').down('gridpanel'),
                                            button = el.up('window').down('button');

                                        if (busy === true) {
                                            return;
                                        }

                                        if (el.getValue().toString().length < 3) {
                                            gridPanel.hide();
                                            return;
                                        }

                                        busy = true;
                                        button.disable();
                                        duplicateNameCheckStore.reload({
                                            params: {
                                                value: el.getValue()
                                            },
                                            callback: function (records) {
                                                busy = false;
                                                button.enable();
                                                if (records.length === 0) {
                                                    gridPanel.hide();
                                                } else {
                                                    gridPanel.show();
                                                }
                                            }
                                        });
                                    }
                                }
                            },
                            duplicateNamesGrid
                        ]
                    },
                    {
                        fieldLabel: t('coreshop_customer_transformer_assignment_form_address_assignment_type'),
                        width: 500,
                        xtype: 'combo',
                        name: 'address_assignment_type',
                        value: Object.keys(data.addresses).length === 0 ? 'keep' : null,
                        store: [
                            ['keep', t('coreshop_customer_transformer_assignment_form_assignment_type_keep')],
                            ['move', t('coreshop_customer_transformer_assignment_form_assignment_type_move')]
                        ],
                        triggerAction: 'all',
                        typeAhead: false,
                        editable: false,
                        allowBlank: false,
                        required: true,
                        readOnly: Object.keys(data.addresses).length === 0,
                        forceSelection: false,
                        queryMode: 'local'
                    },
                    {
                        fieldLabel: t('coreshop_customer_transformer_assignment_form_assignment_address_access_type'),
                        width: 500,
                        xtype: 'combo',
                        name: 'address_access_type',
                        value: 'own_only',
                        store: [
                            ['own_only', t('coreshop_customer_transformer_assignment_form_assignment_address_access_own_only')],
                            ['company_only', t('coreshop_customer_transformer_assignment_form_assignment_address_access_company_only')],
                            ['own_and_company', t('coreshop_customer_transformer_assignment_form_assignment_address_access_own_and_company')]
                        ],
                        triggerAction: 'all',
                        typeAhead: false,
                        editable: false,
                        allowBlank: false,
                        required: true,
                        readOnly: Object.keys(data.addresses).length === 0,
                        forceSelection: true,
                        queryMode: 'local'
                    },
                    {
                        xtype: 'gridpanel',
                        title: t('coreshop_customer_transformer_assignment_form_available_customer_addresses'),
                        viewConfig: {
                            enableTextSelection: true
                        },
                        store: new Ext.data.Store({
                            data: data.addresses,
                            fields: ['id', 'path']
                        }),
                        columns: [
                            {
                                text: t('id'),
                                dataIndex: 'id',
                                flex: 1
                            },
                            {
                                text: t('path'),
                                dataIndex: 'path',
                                flex: 2
                            }
                        ]
                    }
                ]
            }]
        });

        window.show();
    },

    submitAssignment: function (button) {

        var formValues,
            submitValues,
            windowPanel = button.up('window'),
            form = windowPanel.down('form').getForm();

        if (!form.isValid()) {
            return;
        }

        formValues = form.getFieldValues();

        submitValues = {
            addressAssignmentType: formValues['address_assignment_type'],
            addressAccessType: formValues['address_access_type'],
            newCompanyName: formValues['new_company_name']
        };

        this.submitForm(
            Routing.generate('coreshop_admin_customer_company_modifier_dispatch_new_assignment', {customerId: this.customerId}),
            submitValues,
            windowPanel
        );
    }
});
