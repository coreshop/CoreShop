/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.selector.object');
coreshop.selector.object = Class.create(pimcore.element.selector.object, {
    getForm: function () {
        var i;

        //set "Home" object ID for search grid column configuration
        this.object  = new Object();
        this.object.id = 1;

        this.searchType = "search";

        var compositeConfig = {
            xtype: "toolbar",
            items: [{
                xtype: "textfield",
                name: "query",
                width: 340,
                hideLabel: true,
                enableKeyEvents: true,
                listeners: {
                    "keydown" : function (field, key) {
                        if (key.getKey() == key.ENTER) {
                            this.search();
                        }
                    }.bind(this),
                    afterrender: function () {
                        this.focus(true,500);
                    }
                }
            }]
        };

        // classes
        var possibleClassRestrictions = [];
        var classStore = pimcore.globalmanager.get("object_types_store");
        classStore.each(function (rec) {
            possibleClassRestrictions.push(rec.data.text);
        });

        var filterClassStore = [];
        var selectedClassStore = [];
        for (i=0; i<possibleClassRestrictions.length; i++) {
            if(in_array(possibleClassRestrictions[i], this.parent.classes )) {
                filterClassStore.push([possibleClassRestrictions[i], ts(possibleClassRestrictions[i])]);
                selectedClassStore.push(possibleClassRestrictions[i]);
            }
        }

        this.classChangeCombo = new Ext.form.ComboBox({
            store: filterClassStore,
            queryMode: "local",
            name: "class",
            triggerAction: "all",
            editable: true,
            typeAhead: true,
            forceSelection: true,
            selectOnFocus: true,
            value: selectedClassStore[0],
            listeners: {
                select: this.changeClass.bind(this)
            }
        });

        compositeConfig.items.push(this.classChangeCombo);


        // add button
        compositeConfig.items.push({
            xtype: "button",
            iconCls: "pimcore_icon_search",
            text: t("search"),
            handler: this.search.bind(this)
        });

        this.saveColumnConfigButton = new Ext.Button({
            tooltip: t('save_grid_options'),
            iconCls: "pimcore_icon_publish",
            hidden: true,
            handler: function () {
                var asCopy = !(this.settings.gridConfigId > 0);
                this.saveConfig(asCopy)
            }.bind(this)
        });

        this.columnConfigButton = new Ext.SplitButton({
            text: t('grid_options'),
            hidden: true,
            iconCls: "pimcore_icon_table_col pimcore_icon_overlay_edit",
            handler: function () {
                this.openColumnConfig(this.selectedClass, this.classId);
            }.bind(this),
            menu: []
        });

        if (this.parent.config.hasOwnProperty('toolbar')) {
            Ext.each(this.parent.config.toolbar, function(item) {
                compositeConfig.items.push(item);
            });
        }

        compositeConfig.items.push("->");

        // add grid config main button
        compositeConfig.items.push(this.columnConfigButton);

        // add grid config save button
        compositeConfig.items.push(this.saveColumnConfigButton);

        if(!this.formPanel) {
            this.formPanel = new Ext.form.FormPanel({
                region: "north",
                bodyStyle: "padding: 2px;",
                items: [compositeConfig]
            });
        }

        return this.formPanel;
    },

    applyExtraParamsToStore: function () {
        var formValues = this.formPanel.getForm().getFieldValues();

        var proxy = this.store.getProxy();

        proxy.setExtraParam("type", "object");
        proxy.setExtraParam("query", formValues.query);
        proxy.setExtraParam("class", formValues.class);

        if (this.gridLanguage) {
            proxy.setExtraParam("language", this.gridLanguage);
        }

        if (this.parent.config && this.parent.config.context) {
            proxy.setExtraParam("context", Ext.encode(this.parent.config.context));
        }
    },
});

pimcore.element.selector.object.addMethods(pimcore.element.helpers.gridColumnConfig);
