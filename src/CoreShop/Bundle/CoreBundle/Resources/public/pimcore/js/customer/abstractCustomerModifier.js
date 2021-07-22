/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */
pimcore.registerNS('coreshop.core.customer.abstractCustomerModifier');
coreshop.core.customer.abstractCustomerModifier = Class.create({

    customerId: null,
    customerData: null,
    companyId: null,
    companyData: null,

    getSelector: function (entityType) {

        var _ = this;

        pimcore.helpers.itemselector(
            false,
            function (entity) {
                this.loadEntityDetail(entityType, entity.id);
            }.bind(_),
            {
                type: ['object'],
                subtype: {
                    object: ['object']
                },
                specific: {
                    classes: coreshop.stack.coreshop.hasOwnProperty(entityType) ? coreshop.stack.coreshop[entityType] : [coreshop.class_map.coreshop[entityType]]
                }
            }
        );
    },

    loadEntityDetail: function (entityType, entityId) {
        Ext.Ajax.request({
            url: '/admin/coreshop/customer-company-modifier/get-entity-details/' + entityType + '/' + entityId,
            method: 'GET',
            callback: function (request, success, rawResponse) {
                try {
                    var response = Ext.decode(rawResponse.responseText);
                    if (response.success) {
                        this.processNextStep(response.data);
                    } else {
                        Ext.Msg.alert(t('error'), response.message, this.getSelector.bind(this, entityType));
                    }
                } catch (e) {
                    Ext.Msg.alert(t('error'), e);
                }
            }.bind(this)
        });
    },

    validateAssignment: function (endPointParams) {

        Ext.Ajax.request({
            url: '/admin/coreshop/customer-company-modifier/validate-assignment/' + endPointParams.join('/'),
            method: 'GET',
            callback: function (request, success, rawResponse) {
                try {
                    var response = Ext.decode(rawResponse.responseText);
                    if (response.success) {
                        this.buildAssignerLayout(response.data);
                    } else {
                        this.reset();
                        Ext.Msg.alert(t('error'), response.message, function () {
                            this.reset();
                            this.getSelector('customer')
                        }.bind(this));
                    }
                } catch (e) {
                    Ext.Msg.alert(t('error'), e);
                }
            }.bind(this)
        });
    },

    submitForm: function (endPointName, endPointParams, formValues, windowPanel) {

        windowPanel.setLoading(true);

        Ext.Ajax.request({
            url: '/admin/coreshop/customer-company-modifier/' + endPointName + '/' + endPointParams.join('/'),
            method: 'POST',
            params: formValues,
            callback: this.onFormSubmissionComplete.bind(this, windowPanel)
        });
    },

    onFormSubmissionComplete: function (windowPanel, request, success, rawResponse) {
        try {
            var response = Ext.decode(rawResponse.responseText);
            if (response.success) {
                this.onSuccess(windowPanel, response);
            } else {
                this.onError(windowPanel, response);
            }
        } catch (e) {
            Ext.Msg.alert(t('error'), e);
        }
    },

    onSuccess: function (windowPanel, response) {

        windowPanel.close();
        windowPanel.destroy();

        pimcore.elementservice.refreshRootNodeAllTrees('object');

        this.reloadObject(response.customerId);
        this.reloadObject(response.companyId);

        Ext.Msg.alert(t('success'), t('coreshop_customer_transformer_assignment_form_success'));
    },

    onError: function (windowPanel, response) {

        Ext.Msg.alert(t('error'), response.message, function () {

            windowPanel.setLoading(false);

            if (response.formError === true) {
                return;
            }

            windowPanel.close();
            windowPanel.destroy();

            this.reset();
            this.getSelector('customer')
        }.bind(this));
    },

    reloadObject: function (id) {

        window.setTimeout(function (id) {
            pimcore.helpers.openObject(id, 'object');
        }.bind(this, id), 500);

        pimcore.helpers.closeObject(id)

    },

    reset: function () {
        this.customerId = null;
        this.customerData = null;
        this.companyId = null;
        this.companyData = null;
    }
});
