/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
                url: '/admin/CoreShop/product-specific-price/get-config',
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

        return this.dirty;
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
