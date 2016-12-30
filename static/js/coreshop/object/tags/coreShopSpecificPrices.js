/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

pimcore.registerNS("pimcore.object.tags.coreShopSpecificPrices");
pimcore.object.tags.coreShopSpecificPrices = Class.create(pimcore.object.tags.abstract, {

    type: "coreShopSpecificPrices",
    panels : [],

    /**
     * @var array
     */
    conditions: [],

    /**
     * @var array
     */
    actions: [],

    dirty : false,

    initialize: function (data, fieldConfig) {
        this.data = data;
        this.fieldConfig = fieldConfig;
        this.panels = [];
    },

    getGridColumnConfig: function(field) {
        return {header: ts(field.label), width: 150, sortable: false, dataIndex: field.key,
            renderer: function (key, value, metaData, record) {
                this.applyPermissionStyle(key, value, metaData, record);

                return t("not_supported");
            }.bind(this, field.key)};
    },

    getLayoutEdit: function () {
        this.component = this.getEditLayout();

        return this.component;
    },

    getLayoutShow: function () {

        this.component = this.getLayoutEdit();

        this.component.on("afterrender", function () {
            this.component.disable();
        }.bind(this));


        return this.component;
    },

    getName: function () {
        return this.fieldConfig.name;
    },

    getEditLayout : function() {
        if (!this.layout) {
            // create new panel
            this.layout = new Ext.Panel({
                //id: this.layoutId,
                title: this.getTitle(),
                //iconCls: this.iconCls,
                layout : 'fit',
                items: [this.getTabPanel()],
                tools : [
                    {
                        type: 'coreshop-add',
                        tooltip: t('add'),
                        handler : function () {
                            this.panels.push(new pimcore.plugin.coreshop.product.specificprice.object.item(this, {}, -1, 'productSpecificPrice'));
                        }.bind(this)
                    }
                ]
            });

            Ext.Ajax.request({
                url: '/plugin/CoreShop/admin_product-specific-price/get-config',
                method: 'GET',
                success: function (result) {
                    var config = Ext.decode(result.responseText);

                    this.conditions = config.conditions;
                    this.actions = config.actions;

                    this.showPriceRules();
                }.bind(this)
            });
        }

        return this.layout;
    },

    showPriceRules : function() {
        Ext.each(this.data, function(data) {
            var panel = new pimcore.plugin.coreshop.product.specificprice.object.item(this, data, data.id, 'productSpecificPrice');

            this.panels.push(panel);

            panel.panel.on('beforedestroy', function () {
                var index = this.panels.indexOf(panel);
                this.panels.splice(index, 1);

                this.dirty = true;
            }.bind(this));
        }.bind(this));

        if(this.panels.length > 0) {
            this.getTabPanel().setActiveItem(this.panels[0].panel);
        }
    },

    getTabPanel : function() {
        if(!this.panel) {
            this.panel = new Ext.TabPanel({
                region: 'center',
                border: false
            });
        }

        return this.panel;
    },

    getValue: function () {
        if(this.isRendered()) {
            var data = [];

            Ext.each(this.panels, function(panel) {
                data.push(panel.getSaveData());
            });

            return data;
        }
    },

    isDirty:function () {
        for(var i = 0; i < this.panels.length; i++) {
            if(this.panels[i].isDirty()) {
                return true;
            }
        }

        if(this.dirty) {
            return true;
        }

        return false;
    },

    getActions : function() {
        return this.actions;
    },

    getConfig : function() {
        return this.config;
    },

    getConditions : function() {
        return this.conditions;
    }
});
