/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('pimcore.object.tags.coreShopProductSpecificPriceRules');
pimcore.object.tags.coreShopProductSpecificPriceRules = Class.create(pimcore.object.tags.abstract, {

    type: 'coreShopProductSpecificPriceRules',
    panels: [],

    /**
     * @var array
     */
    conditions: [],

    /**
     * @var array
     */
    actions: [],

    dirty: false,

    initialize: function (data, fieldConfig) {
        this.data = data.rules;
        this.fieldConfig = fieldConfig;
        this.panels = [];
        this.conditions = data.conditions;
        this.actions = data.actions;
        this.eventDispatcherKey = pimcore.eventDispatcher.registerTarget(this.eventDispatcherKey, this);
    },

    postSaveObject: function (object, task) {

        var fieldName = this.getName();

        if (object.id !== this.object.id) {
            return;
        }

        if (this.isDirty()) {
            this.reloadPriceRuleData(object, task, fieldName);
        }
    },

    reloadPriceRuleData: function (object, task, fieldName) {
        this.component.setLoading(true);
        Ext.Ajax.request({
            url: '/admin/object/get',
            params: {id: object.id},
            ignoreErrors: true,
            success: function (response) {

                this.dirty = false;

                var refreshedObject = null,
                    refreshedObjectData = null;
                try {
                    refreshedObject = Ext.decode(response.responseText);
                    if (!refreshedObject.hasOwnProperty('data') || !refreshedObject.data.hasOwnProperty(fieldName)) {
                        this.component.setLoading(false);
                        return;
                    }
                    refreshedObjectData = refreshedObject.data[fieldName];
                } catch (e) {
                    console.log(e);
                }

                this.component.setLoading(false);
                if (refreshedObjectData !== null) {
                    this.dispatchPostSaveToPanels(object, refreshedObjectData, task, fieldName);
                }
            }.bind(this),
            failure: function () {
                this.component.setLoading(false);
            }.bind(this),
        });
    },

    dispatchPostSaveToPanels: function (object, refreshedData, task, fieldName) {

        var refreshAllPanels = false;

        if (!refreshedData.hasOwnProperty('rules') || !Ext.isArray(refreshedData.rules)) {
            return;
        }

        Ext.each(this.panels, function (panel) {
            if (panel.getId() === null) {
                refreshAllPanels = true;
                return false;
            }
        });

        if (refreshAllPanels === true) {
            this.rebuildPriceRules(refreshedData.rules);
        } else {
            this.rebuildPriceRuleData(object, refreshedData.rules, task, fieldName);
        }
    },

    rebuildPriceRuleData: function (object, refreshedRuleData, task, fieldName) {
        Ext.each(this.panels, function (panelClass) {
            var newRulePanelData = null;
            Ext.Array.each(refreshedRuleData, function (ruleData) {
                if (ruleData.hasOwnProperty('id') && ruleData.id === panelClass.getId()) {
                    newRulePanelData = ruleData;
                    return false;
                }
            });
            if (newRulePanelData !== null) {
                panelClass.postSaveObject(object, newRulePanelData, task, fieldName);
            }
        });
    },

    rebuildPriceRules: function (refreshedRuleData) {

        var lastActiveItem = this.getTabPanel().getActiveTab(),
            activeTabIndex = this.getTabPanel().items.findIndex('id', lastActiveItem.id);

        this.getTabPanel().removeAll();

        this.data = refreshedRuleData;
        this.panels = [];

        this.showPriceRules(activeTabIndex);
    },


    getGridColumnConfig: function (field) {
        return {
            header: ts(field.label), width: 150, sortable: false, dataIndex: field.key,
            renderer: function (key, value, metaData, record) {
                this.applyPermissionStyle(key, value, metaData, record);

                return t('not_supported');
            }.bind(this, field.key)
        };
    },

    getLayoutEdit: function () {
        this.component = this.getEditLayout();

        return this.component;
    },

    getLayoutShow: function () {

        this.component = this.getLayoutEdit();

        this.component.on('afterrender', function () {
            this.component.disable();
        }.bind(this));


        return this.component;
    },

    getName: function () {
        return this.fieldConfig.name;
    },

    getEditLayout: function () {
        if (!this.layout) {
            // create new panel
            this.layout = new Ext.Panel({
                //id: this.layoutId,
                title: this.getTitle(),
                //iconCls: this.iconCls,
                layout: 'fit',
                items: [this.getTabPanel()],
                tools: [
                    {
                        type: 'coreshop-add',
                        tooltip: t('add'),
                        handler: function () {
                            this.panels.push(new coreshop.product.specificprice.object.item(this, {}, -1, 'productSpecificPriceRule'));
                        }.bind(this)
                    }
                ]
            });

            this.showPriceRules();
        }

        return this.layout;
    },

    showPriceRules: function (lastActiveItemIndex) {
        Ext.each(this.data, function (data) {
            this.createItemPanel(data, data.id);
        }.bind(this));

        if (this.panels.length > 0) {
            var activePanel = lastActiveItemIndex && this.panels[lastActiveItemIndex] ? this.panels[lastActiveItemIndex].panel : this.panels[0].panel;
            this.getTabPanel().setActiveItem(activePanel);
        }
    },

    createItemPanel: function (data, id) {
        var panelItem = new coreshop.product.specificprice.object.item(this, data, id, 'productSpecificPriceRule');

        this.panels.push(panelItem);

        panelItem.panel.on('beforedestroy', function () {
            var index = this.panels.indexOf(panelItem);
            this.panels.splice(index, 1);

            this.dirty = true;
        }.bind(this));

        return panelItem;
    },

    getTabPanel: function () {
        if (!this.panel) {
            this.panel = new Ext.TabPanel({
                region: 'center',
                border: false
            });
        }

        return this.panel;
    },

    getValue: function () {
        if (this.isRendered()) {
            var data = [];

            Ext.each(this.panels, function (panel) {
                data.push(panel.getSaveData());
            });

            return data;
        }
    },

    isDirty: function () {
        for (var i = 0; i < this.panels.length; i++) {
            if (this.panels[i].isDirty()) {
                return true;
            }
        }

        return this.dirty;
    },

    getActions: function () {
        return this.actions;
    },

    getConfig: function () {
        return this.config;
    },

    getConditions: function () {
        return this.conditions;
    }
});
