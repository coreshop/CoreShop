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

pimcore.registerNS('coreshop.resource.item');
coreshop.resource.item = Class.create({

    iconCls: '',

    url: {
        save: ''
    },

    multiShopSettings: false,

    initialize: function (parentPanel, data, panelKey, type) {
        this.parentPanel = parentPanel;
        this.data = data;
        this.panelKey = panelKey;
        this.type = type;

        this.initPanel();
    },

    initPanel: function () {
        this.panel = this.getPanel();

        this.panel.on('beforedestroy', function () {
            delete this.parentPanel.panels[this.panelKey];
        }.bind(this));

        this.parentPanel.getTabPanel().add(this.panel);
        this.parentPanel.getTabPanel().setActiveItem(this.panel);
    },

    destroy: function () {
        if (this.panel) {
            this.panel.destroy();
        }
    },

    getPanel: function () {
        var items = this.getItems();

        panel = new Ext.panel.Panel({
            title: this.getTitleText(),
            closable: true,
            iconCls: this.iconCls,
            layout: 'border',
            items: items
        });

        return panel;
    },

    getTitleText: function () {
        return this.data.name;
    },

    activate: function () {
        this.parentPanel.getTabPanel().setActiveItem(this.panel);
    },

    getItems: function () {
        return [];
    },

    getSaveData: function () {
        return {};
    },

    save: function (callback) {
        var me = this,
            data;

        if (this.isValid()) {
            var saveData = this.getSaveData();

            saveData['id'] = this.data.id;
            saveData = coreshop.helpers.convertDotNotationToObject(saveData);

            if (saveData.hasOwnProperty('stores')) {
                var stores = [];

                saveData.stores.forEach(function (store) {
                    stores.push(store + "");
                });

                saveData.stores = stores;
            }

            Ext.Ajax.request({
                url: this.url.save,
                method: 'post',
                jsonData: saveData,
                success: function (response) {
                    try {
                        if (this.parentPanel.store) {
                            this.parentPanel.store.load();
                        }

                        this.parentPanel.refresh();

                        var res = Ext.decode(response.responseText);

                        this.postSave(res);

                        if (Ext.isFunction(callback)) {
                            callback(res);
                        }

                        if (res.success) {
                            pimcore.helpers.showNotification(t('success'), t('coreshop_save_success'), 'success');

                            this.data = res.data;

                            this.panel.setTitle(this.getTitleText());
                        } else {
                            pimcore.helpers.showNotification(t('error'), t('coreshop_save_error'),
                                'error', res.message);
                        }
                    } catch (e) {
                        pimcore.helpers.showNotification(t('error'), t('coreshop_save_error'), 'error');
                    }
                }.bind(this)
            });
        }
    },

    postSave: function (result) {

    },

    isValid: function () {
        return true;
    }
});
