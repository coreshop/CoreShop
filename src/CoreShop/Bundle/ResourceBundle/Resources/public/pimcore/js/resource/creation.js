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
