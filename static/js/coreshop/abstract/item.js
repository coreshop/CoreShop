/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS("pimcore.plugin.coreshop.abstract.item");

pimcore.plugin.coreshop.abstract.item = Class.create({

    iconCls : '',

    url : {
        save : ''
    },

    initialize: function (parentPanel, data, panelKey, type) {
        this.parentPanel = parentPanel;
        this.data = data;
        this.panelKey = panelKey;
        this.type = type;

        this.initPanel();
    },

    initPanel: function () {
        this.panel = this.getPanel();

        this.panel.on("beforedestroy", function () {
            delete this.parentPanel.panels[this.panelKey];
        }.bind(this));

        this.parentPanel.getTabPanel().add(this.panel);
        this.parentPanel.getTabPanel().setActiveItem(this.panel);
    },

    getPanel: function() {
        panel = new Ext.panel.Panel({
            title: this.getTitleText(),
            closable: true,
            iconCls: this.iconCls,
            layout: "border",
            items : this.getItems()
        });

        return panel;
    },

    getTitleText : function() {
        return this.data.name;
    },

    activate : function() {
        this.parentPanel.getTabPanel().setActiveItem(this.panel);
    },

    getItems : function() {
        return [];
    },

    getSaveData : function() {
        return {};
    },

    save: function ()
    {
        var saveData = this.getSaveData();

        saveData['id'] = this.data.id;

        Ext.Ajax.request({
            url: this.url.save,
            method: "post",
            params: saveData,
            success: function (response) {
                try {
                    this.postSave();

                    if(this.parentPanel.store) {
                        this.parentPanel.store.load();
                    }

                    if(pimcore.globalmanager.exists("coreshop_" + this.type)) {
                        pimcore.globalmanager.get("coreshop_" + this.type).load();
                    }

                    var res = Ext.decode(response.responseText);
                    if (res.success) {
                        pimcore.helpers.showNotification(t("success"), t("coreshop_save_success"), "success");
                    } else {
                        pimcore.helpers.showNotification(t("error"), t("coreshop_save_error"),
                            "error", res.message);
                    }
                } catch(e) {
                    pimcore.helpers.showNotification(t("error"), t("coreshop_save_error"), "error");
                }
            }.bind(this)
        });
    },

    postSave : function() {

    }
});