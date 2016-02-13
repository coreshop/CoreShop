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

pimcore.registerNS("pimcore.plugin.coreshop.filters.item");

pimcore.plugin.coreshop.filters.item = Class.create(pimcore.plugin.coreshop.abstract.item, {

    iconCls : 'coreshop_icon_filters',

    url : {
        save : '/plugin/CoreShop/admin_Filter/save'
    },

    getPanel: function() {
        panel = new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls : this.iconCls,
            buttons: [{
                text: t("save"),
                iconCls: "pimcore_icon_apply",
                handler: this.save.bind(this)
            }],
            items: this.getItems()
        });

        return panel;
    },

    getItems : function() {
        /*this.actions = new pimcore.plugin.coreshop.pricerules.action(this.parentPanel.actions);
        this.conditions = new pimcore.plugin.coreshop.pricerules.condition(this.parentPanel.conditions);

        var items = [
            this.getSettings(),
            this.conditions.getLayout(),
            this.actions.getLayout()
        ];

        // add saved conditions
        if(this.data.conditions)
        {
            Ext.each(this.data.conditions, function(condition) {
                this.conditions.addCondition(condition.type, condition);
            }.bind(this));
        }

        // add saved actions
        if(this.data.actions)
        {
            Ext.each(this.data.actions, function(action) {
                this.actions.addAction(action.type, action);
            }.bind(this));
        }

        return items;*/
        return [];
    },

    getSettings: function () {
        var data = this.data;

        this.settingsForm = Ext.create('Ext.form.Panel', {
            iconCls: "coreshop_price_rule_settings",
            title: t("settings"),
            bodyStyle: "padding:10px;",
            autoScroll: true,
            border:false,
            items: [{
                xtype: "textfield",
                name: "label",
                fieldLabel: t("label"),
                width: 250,
                value: data.label
            }, {
                xtype: "textfield",
                name: "code",
                fieldLabel: t("code"),
                width: 250,
                value: data.code
            }, {
                xtype: "textarea",
                name: "description",
                fieldLabel: t("description"),
                width: 400,
                height: 100,
                value: data.description
            }, {
                xtype: "checkbox",
                name: "active",
                fieldLabel: t("active"),
                checked: this.data.active == "1"
            }, {
                xtype: "checkbox",
                name: "highlight",
                fieldLabel: t("highlight"),
                checked: this.data.highlight == "1"
            }]
        });

        return this.settingsForm;
    },

    getSaveData : function() {
        var saveData = {};

        // general settings
        saveData["settings"] = this.settingsForm.getForm().getFieldValues();
        saveData["conditions"] = this.conditions.getConditionsData();
        saveData["actions"] = this.actions.getActionsData();

        return {
            data : Ext.encode(saveData)
        };
    }
});
