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

        var configItems = [];

        configItems.push(new Ext.form.TextField({
            fieldLabel : t('coreshop_index_field_name'),
            name : 'name',
            length : 255,
            width : 200,
            value : record.data.name ? record.data.name : record.data.key
        }));

        //TODO: Load all available getters and show in combo
        configItems.push(new Ext.form.TextField({
            fieldLabel : t('coreshop_index_field_getter'),
            name : 'getter',
            length : 255,
            width : 200,
            value : record.data.getter
        }));

        var nodeTypeItems = this.getObjectTypeItems(record);

        if(nodeTypeItems.length > 0) {
            nodeTypeItems.forEach(function(item) {
                configItems.push(item);
            });
        }

        this.configPanel = new Ext.form.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: configItems,
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

        this.window.show();
    },

    commitData: function() {

        Ext.Object.each(this.configPanel.getForm().getFieldValues(), function(key, value) {
            this.record.set(key, value);
        }.bind(this));

        if(this.record.data.name !== this.record.data.text) {
            this.record.set("text", this.record.data.name);
        }

        this.window.close();
    }
});