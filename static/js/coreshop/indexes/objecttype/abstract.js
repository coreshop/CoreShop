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

pimcore.registerNS("pimcore.plugin.coreshop.indexes.objecttype.abstract");

pimcore.plugin.coreshop.indexes.objecttype.abstract = Class.create({
    initialize: function() {

    },

    getObjectTypeItems : function(record) {
        return [];
    },

    getConfigDialog : function(record) {
        this.record = record;

        var fieldSetItems = [];

        fieldSetItems.push(new Ext.form.TextField({
            fieldLabel : t('coreshop_index_field_name'),
            name : 'name',
            length : 255,
            value : record.data.name ? record.data.name : record.data.key
        }));

        fieldSetItems.push(new Ext.form.ComboBox({
            fieldLabel : t('coreshop_index_field_getter'),
            name : 'getter',
            length : 255,
            value : record.data.getter,
            store : pimcore.globalmanager.get("coreshop_index_getters"),
            valueField : 'type',
            displayField : 'name',
            queryMode : 'local',
            listeners : {
                change : function(combo, newValue) {
                    this.getGetterPanel().removeAll();

                    this.getGetterPanelLayout(newValue);
                }.bind(this)
            }
        }));

        var nodeTypeItems = this.getObjectTypeItems(record);

        if(nodeTypeItems.length > 0) {
            nodeTypeItems.forEach(function(item) {
                fieldSetItems.push(item);
            });
        }

        this.configForm = new Ext.form.FormPanel({
            items : fieldSetItems,
            layout: "form",
            defaults: {anchor: '90%'}
        });

        this.configPanel = new Ext.panel.Panel({
            layout: "form",
            scrollable : true,
            items:
                [
                    this.configForm,
                    this.getGetterPanel()
                ],
            buttons: [{
                text: t("apply"),
                iconCls: "pimcore_icon_apply",
                handler: function () {
                    this.commitData();
                }.bind(this)
            }]
        });

        this.window = new Ext.Window({
            width: 400,
            height: 400,
            resizeable : true,
            modal: true,
            title: t('coreshop_index_field') + " (" + this.record.data.key + ")",
            layout: "fit",
            items: [this.configPanel]
        });

        this.getGetterPanelLayout(record.data.getter);

        this.window.show();
    },

    commitData: function() {
        var form = this.configForm.getForm();
        var getterForm = this.getGetterPanel().getForm();

        Ext.Object.each(form.getFieldValues(), function(key, value) {
            this.record.set(key, value);
        }.bind(this));

        if(this.getGetterPanel().isVisible()) {
            this.record.set("getterConfig", getterForm.getFieldValues());
        }

        if(this.record.data.name !== this.record.data.text) {
            this.record.set("text", this.record.data.name);
        }

        this.window.close();
    },

    getGetterPanel : function() {
        if(!this.getterPanel) {
            this.getterPanel = new Ext.form.FormPanel({
                defaults: {anchor: '90%'},
                layout: "form"
            });
        }

        return this.getterPanel;
    },

    getGetterPanelLayout : function(type) {
        if(type) {
            type = type.toLowerCase();
            //Check if some class for getterPanel is available
            if (pimcore.plugin.coreshop.indexes.getters[type]) {
                var getter = new pimcore.plugin.coreshop.indexes.getters[type];

                this.getGetterPanel().add(getter.getLayout(this.record));
                this.getGetterPanel().show();
            }
            else {
                this.getGetterPanel().hide()
            }
        }
        else {
            this.getGetterPanel().hide()
        }
    }
});