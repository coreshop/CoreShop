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

pimcore.registerNS('coreshop.resource.creation');
coreshop.resource.creation = Class.create({
    url: null,
    route: null,
    type: null,
    callback: Ext.emptyFn,
    options: {
        prefix: '',
        params: {}
    },

    initialize: function (options, callback) {
        this.options = Ext.isDefined(options) ? Ext.mergeIf(options, this.options) : this.options;
        this.callback = Ext.isDefined(callback) ? callback : Ext.emptyFn;

        this.window = new Ext.Window({
            width: 670,
            height: '80%',
            modal: true,
            resize: false,
            title: t('coreshop_' + this.type + '_create'),
            layout: 'fit',
            items: this.getForm()
        });
    },

    show: function() {
        this.window.show();
    },

    close: function() {
        this.window.close();
    },

    getForm: function() {
        this.form = Ext.create('Ext.form.Panel', {
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border: false,
            fieldDefaults: {
                labelWidth: 300,
                width: 600
            },
            items: this.getSettings(),
            buttons: [{
                text: t('create'),
                iconCls: 'pimcore_icon_apply',
                handler: function (btn) {
                    if (btn.up('form').getForm().isValid()) {
                        var params = btn.up('form').getForm().getFieldValues();
                        params = coreshop.helpers.convertDotNotationToObject(params);

                        btn.setDisabled(true);
                        this.window.setLoading();

                        params = Ext.merge(params, this.options.params);

                        Ext.Ajax.request({
                            url: this.route ? Routing.generate(this.route) : this.url,
                            method: 'post',
                            jsonData: params,
                            success: function (response) {
                                var res = Ext.decode(response.responseText);
                                if (res.success) {
                                    this.callback(res.id);
                                    this.window.close();
                                } else {
                                    this.window.setLoading(false);
                                    btn.setDisabled(false);
                                    pimcore.helpers.showNotification(t('error'), (res.message ? res.message : 'error'), 'error');
                                }
                            }.bind(this),
                            failure: function (response) {
                                this.window.setLoading(false);
                                btn.setDisabled(false);
                            }.bind(this)
                        });
                    }
                }.bind(this)
            }]
        });

        return this.form;
    },

    getSettings: function () {
        return [];
    },
});
