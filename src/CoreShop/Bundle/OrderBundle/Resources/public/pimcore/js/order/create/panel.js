/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.order.order.create');
pimcore.registerNS('coreshop.order.order.create.panel');
coreshop.order.order.create.panel = Class.create({
    type: 'order',
    steps: {},
    eventManager: null,
    customerId: null,
    customerDetail: null,

    initialize: function () {
        var me = this;

        me.eventManager = new CoreShop.resource.EventManager();
        me.eventManager.on('preview', function () {
            me.layout.setLoading(true);

            Ext.Ajax.request({
                url: Routing.generate('coreshop_admin_order_preview'),
                method: 'post',
                jsonData: me.getPreviewValues(),
                callback: function (request, success, response) {
                    response = Ext.decode(response.responseText);

                    if (response.success) {
                        Ext.Object.each(me.steps, function (key, value) {
                            value.setPreviewData(response.data);
                        });
                    }
                    me.layout.setLoading(false);

                    me.eventManager.fireEvent('validation');
                }.bind(this)
            });
        });
        me.eventManager.on('validation', function () {
            var valid = true;

            Ext.Object.each(me.steps, function (key, value) {
                if (!value.isValid()) {
                    valid = false;
                    return false;
                }
            });

            if (valid) {
                me.createButton.enable();
            }
            else {
                me.createButton.disable();
            }
        });

        this.loadSaleRelator();
    },

    loadSaleRelator: function() {
        var me = this,
            toolbarConfig = [];

        toolbarConfig.push(new Ext.Button({
            text: t("coreshop_order_create_customer"),
            handler: this.createRelator.bind(this),
            iconCls: "pimcore_icon_add",
            enableToggle: true
        }));

        this.selector = new coreshop.selector.selector(
            false,
            function (customer) {
                me.loadCustomerDetail(customer.id);
            },
            {
                classes: coreshop.stack.coreshop.customer
            },
            {
                toolbar: toolbarConfig
            }
        );
    },

    createRelator: function() {
        var me = this;
        new coreshop.order.order.create.customer({prefix: 'customer.'}, function(id) {
            me.selector.window.close();
            me.loadCustomerDetail(id);
        }).show();
    },

    loadCustomerDetail: function (customerId) {
        this.customerId = customerId;

        Ext.Ajax.request({
            url: Routing.generate('coreshop_admin_order_get_customer_details'),
            method: 'post',
            params: {
                customerId: customerId
            },
            callback: function (request, success, response) {
                try {
                    response = Ext.decode(response.responseText);

                    if (response.success) {
                        this.customerDetail = response.customer;

                        this.getLayout();
                    } else {
                        Ext.Msg.alert(t('error'), response.message);
                    }
                }
                catch (e) {
                    Ext.Msg.alert(t('error'), e);
                }
            }.bind(this)
        });
    },

    getStep: function (step) {
        return this.steps[step];
    },

    getLayout: function () {
        if (!this.layout) {

            this.layoutId = Ext.id();

            this.createButton = new Ext.button.Button({
                iconCls: 'pimcore_icon_save',
                text: t('create'),
                disabled: true,
                handler: this.createSale.bind(this)
            });

            this.resetButton = new Ext.button.Button({
                iconCls: 'pimcore_icon_delete',
                text: t('reset'),
                disabled: false,
                handler: this.reset.bind(this)
            });

            this.refreshButton = new Ext.button.Button({
                iconCls: 'pimcore_icon_refresh',
                text: t('refresh'),
                disabled: false,
                handler: this.refresh.bind(this)
            });

            // create new panel
            this.layout = new Ext.panel.Panel({
                id: this.layoutId,
                title: t('coreshop_' + this.type + '_create'),
                iconCls: 'coreshop_icon_' + this.type + '_create',
                border: false,
                layout: 'border',
                autoScroll: true,
                closable: true,
                items: [this.getPanel()],
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'bottom',
                    items: [
                        '->',
                        this.createButton
                    ]
                }, {
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [
                        this.resetButton,
                        this.refreshButton
                    ]
                }]
            });

            // add panel to pimcore panel tabs
            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.layout);
            tabPanel.setActiveItem(this.layoutId);

            // update layout
            pimcore.layout.refresh();
        }

        return this.layout;
    },

    getPanel: function () {
        var me = this,
            defaults = {
                style: this.borderStyle,
                cls: 'coreshop-panel',
                bodyPadding: 5
            },
            stepIdentifier = me.getStepIdentifier(),
            stepKeys = Object.keys(stepIdentifier),
            steps = [],
            stepLayouts = [];

        stepKeys.forEach(function (stepName) {
            var step = new stepIdentifier[stepName](me, me.eventManager);

            steps.push(step);
            me.steps[stepName] = step;
        });

        Ext.Array.sort(steps, function (stepA, stepB) {
            var stepAPriority = stepA.getPriority();
            var stepBPriority = stepB.getPriority();

            if (stepAPriority === stepBPriority) {
                return 0;
            }

            return stepAPriority > stepBPriority ? 1 : -1;
        });

        stepLayouts = steps.map(function (step) {
            return step.getLayout();
        });

        this.panel = Ext.create('Ext.container.Container', {
            border: false,
            items: stepLayouts,
            padding: '5 20 20 20',
            region: 'center',
            defaults: defaults
        });

        return this.panel;
    },

    getValues: function () {
        var values = {
            customer: this.customerId,
            saleType: this.type
        };

        Ext.Object.each(this.steps, function (key, value) {
            values = Ext.apply(values, value.getValues());
        });

        return values;
    },

    getPreviewValues: function () {
        var values = {
            customer: this.customerId,
            saleType: this.type
        };

        Ext.Object.each(this.steps, function (key, value) {
            values = Ext.apply(values, value.getPreviewValues());
        });

        return values;
    },

    reset: function() {
        this.eventManager.suspendEvents();

        Ext.Object.each(this.steps, function (key, step) {
            step.reset();
        });

        this.eventManager.resumeEvents();
    },

    refresh: function() {
        this.eventManager.fireEvent('preview');
    },

    prepareSuccessMessage: function(message, response) {
        if (response.hasOwnProperty('reviseLink') && response.reviseLink) {
            message += '<div class="coreshop-order-create-revise">';
            message += '<span class="coreshop-order-create-revise-desc">' + t('coreshop_creating_order_finished_revise_link') + '</span>';
            message += '<span class="coreshop-order-create-revise-link">' + response.reviseLink + '</span>';
            message += '</div>';
        }

        return message;
    },

    createSale: function () {
        this.layout.setLoading(t('coreshop_creating_' + this.type));

        Ext.Ajax.request({
            url: Routing.generate('coreshop_admin_order_create'),
            method: 'post',
            jsonData: this.getValues(),
            callback: function (request, success, response) {
                try {
                    response = Ext.decode(response.responseText);

                    if (response.success) {
                        var message = t('coreshop_creating_' + this.type + '_finished_detail');

                        message = this.prepareSuccessMessage(message, response);

                        var win = new Ext.Window({
                            modal: true,
                            iconCls: 'coreshop_icon_' + this.type + '_create',
                            title: t('coreshop_creating_' + this.type + '_finished'),
                            width: 600,
                            minWidth: 250,
                            minHeight: 110,
                            maxHeight: 500,
                            closable: false,
                            resizable: false,
                            items: [
                                {
                                    xtype: 'container',
                                    padding: 10,
                                    style: {
                                        overflow: 'hidden'
                                    },
                                    items: [
                                        {
                                            xtype: 'component',
                                            cls: Ext.baseCSSPrefix + 'message-box-icon-text',
                                            html: message,
                                        }
                                    ]
                                }
                            ],
                            dockedItems: [
                                {
                                    xtype: 'toolbar',
                                    ui: 'footer',
                                    dock: 'bottom',
                                    focusableContainer: false,
                                    ariaRole: null,
                                    layout: {
                                        pack: 'center'
                                    },
                                    items: [
                                        {
                                            handler: function() {
                                                win.close();
                                                this.layout.destroy();
                                            }.bind(this),
                                            scope: this,
                                            text: t('coreshop_sale_action_close_editor'),
                                            minWidth: 75
                                        },
                                        {
                                            handler: function() {
                                                win.close();
                                                this.layout.destroy();

                                                this.__proto__.constructor();
                                            },
                                            scope: this,
                                            text: t('coreshop_sale_action_add_another'),
                                            minWidth: 75
                                        },
                                        {
                                            handler: function() {
                                                win.close();

                                                this.reset();
                                            }.bind(this),
                                            scope: this,
                                            text: t('coreshop_sale_action_add_another_same_customer'),
                                            minWidth: 75
                                        },
                                        {
                                            handler: function() {
                                                win.close();
                                                this.layout.destroy();

                                                coreshop.order.helper.openSale(response.id, this.type);
                                            }.bind(this),
                                            scope: this,
                                            text: t('coreshop_sale_action_open_' + this.type),
                                            minWidth: 75
                                        }
                                    ]
                                }
                            ],
                        }).show();
                    } else {
                        Ext.Msg.alert(t('error'), response.message);
                    }
                }
                catch (e) {
                    Ext.Msg.alert(t('error'), e);
                }

                this.layout.setLoading(false);
            }.bind(this)
        });
    },

    getStepIdentifier: function () {
        return coreshop.order.order.create.step;
    }
});
