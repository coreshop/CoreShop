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


pimcore.registerNS("pimcore.plugin.coreshop.pricerule.item");
pimcore.plugin.coreshop.pricerule.item = Class.create({

    /**
     * pimcore.plugin.coreshop.pricerule.panel
     */
    parent: {},


    /**
     * constructor
     * @param parent
     * @param data
     */
    initialize: function(parent, data) {
        this.parent = parent;
        this.data = data;
        this.currentIndex = 0;

        this.tabPanel = new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            id: "pimcore_targeting_panel_" + this.data.id,
            buttons: [{
                text: t("save"),
                iconCls: "pimcore_icon_apply",
                handler: this.save.bind(this)
            }],
            items: [
                this.getSettings(),
                this.getConditions(),
                this.getActions()
            ]
        });


        // add saved conditions
        if(this.data.conditions)
        {
            var list = this;
            Ext.each(this.data.conditions, function(condition) {
                list.addCondition(condition.type, condition);
            });
        }

        // add saved actions
        if(this.data.actions)
        {
            var list = this;
            Ext.each(this.data.actions, function(action){
                list.addAction(action.type, action);
            });
        }

        // ...
        var panel = this.parent.getTabPanel();
        panel.add(this.tabPanel);
        panel.setActiveItem(this.tabPanel);
        panel.updateLayout();
    },

    activate : function() {
        this.parent.getTabPanel().setActiveItem(this.tabPanel);
    },

    /**
     * Basic rule Settings
     * @returns Ext.form.FormPanel
     */
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

    /**
     * @returns Ext.Panel
     */
    getConditions: function() {
        // init
        var _this = this;
        var addMenu = [];

        // show only defined conditions
        Ext.each(this.parent.condition, function (condition) {

            if(condition == "abstract")
                return;

            addMenu.push({
                iconCls: "coreshop_price_rule_icon_condition_" + condition,
                text: t("coreshop_condition_" + condition),
                handler: _this.addCondition.bind(_this, condition, null)
            });

        });


        this.conditionsContainer = new Ext.Panel({
            iconCls: "coreshop_price_rule_conditions",
            title: t("conditions"),
            autoScroll: true,
            style : 'padding: 10px',
            forceLayout: true,
            tbar: [{
                iconCls: "pimcore_icon_add",
                menu: addMenu
            }],
            border: false
        });

        return this.conditionsContainer;
    },

    /**
     * @returns {*}
     * @todo
     */
    getActions: function () {
        // init
        var _this = this;
        var addMenu = [];

        // show only defined actions
        Ext.each(this.parent.action, function (action) {

            if(action == "abstract")
                return;

            addMenu.push({
                iconCls: "coreshop_price_rule_icon_action_" + action,
                text: t("coreshop_action_" + action),
                handler: _this.addAction.bind(_this, action, null)
            });
        });


        this.actionsContainer = new Ext.Panel({
            iconCls: "coreshop_price_rule_actions",
            title: t("actions"),
            autoScroll: true,
            forceLayout: true,
            style : 'padding: 10px',
            tbar: [{
                iconCls: "pimcore_icon_add", // plugin_onlineshop_pricing_action
                menu: addMenu
            }],
            border: false
        });

        return this.actionsContainer;
    },


    /**
     * add condition item
     * @param type
     * @param data
     */
    addCondition: function (type, data) {

        // create condition
        var item = new pimcore.plugin.coreshop.pricerule.conditions[type](this, data);

        // add logic for brackets
        var tab = this;

        this.conditionsContainer.add(item.getLayout());
        this.conditionsContainer.updateLayout();
    },

    /**
     * add action item
     * @param type
     * @param data
     */
    addAction: function (type, data) {

        var item = new pimcore.plugin.coreshop.pricerule.actions[type](this, data);

        this.actionsContainer.add(item.getLayout());
        this.actionsContainer.updateLayout();
    },

    /**
     * save config
     * @todo
     */
    save: function () {
        var saveData = {};

        // general settings
        saveData["settings"] = this.settingsForm.getForm().getFieldValues();

        // get defined conditions
        var conditionsData = [];
        var conditions = this.conditionsContainer.items.getRange();
        for (var i=0; i<conditions.length; i++) {
            var condition = {};

            var conditionItem = conditions[i];
            var conditionClass = conditionItem.xparent;
            var form = conditionClass.form;

            for(var c=0; c < form.items.length; c++)
            {
                var item = form.items.get(c);

                try {
                    condition[item.getName()] = item.getValue();
                }
                catch (e)
                {

                }

            }

            condition['type'] = conditions[i].xparent.type;
            conditionsData.push(condition);
        }
        saveData["conditions"] = conditionsData;

        // get defined actions
        var actionData = [];
        var actions = this.actionsContainer.items.getRange();
        for (var i=0; i < actions.length; i++)
        {
            var action = {};

            var actionItem = actions[i];
            var actionClass = actionItem.xparent;
            var form = actionClass.form;

            for(var c=0; c < form.items.length; c++)
            {
                var item = form.items.get(c);

                try {
                    action[item.getName()] = item.getValue();
                }
                catch (e)
                {

                }

            }

            action['type'] = actions[i].xparent.type;
            actionData.push(action);
        }
        saveData["actions"] = actionData;

        // send data
        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_PriceRules/save",
            params: {
                id: this.data.id,
                data: Ext.encode(saveData)
            },
            method: "post",
            success: this.saveOnComplete.bind(this)
        });
    },

    /**
     * saved
     */
    saveOnComplete: function () {
        this.parent.getTree().getStore().reload();

        pimcore.helpers.showNotification(t("success"), t("coreshop_price_rule_saved_successfully"), "success");
    }
});
