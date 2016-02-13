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

pimcore.registerNS("pimcore.plugin.coreshop.indexes.elements.mysql.default");

pimcore.plugin.coreshop.indexes.elements.mysql.default = Class.create(pimcore.plugin.coreshop.indexes.elements.abstract, {
    type: "value",
    class: "default",

    getConfigDialog: function(record) {
        this.record = record;

        var configItems = [];

        configItems.push(new Ext.form.TextField({
            fieldLabel : t('coreshop_index_field_name'),
            name : 'name',
            length : 255,
            width : 200,
            value : record.data.config ? record.data.config.name : record.data.text
        }));

        configItems.push(new Ext.form.TextField({
            fieldLabel : t('coreshop_index_field_type'),
            name : 'type',
            length : 255,
            width : 200,
            value : record.data.config ? record.data.config.type : null
        }));

        configItems.push(new Ext.form.TextField({
            fieldLabel : t('coreshop_index_field_getter'),
            name : 'getter',
            length : 255,
            width : 200,
            value : record.data.config ? record.data.config.getter : null
        }));

        //Check if its an brick
        if(record.data.key.indexOf("~") >= 0) {
            configItems.push(new Ext.form.TextField({
                fieldLabel : t('coreshop_index_field_brickfield'),
                name : 'brickfield',
                length : 255,
                width : 200,
                value : record.data.config ? record.data.config.brickfield : null
            }));
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
        this.record.data.config = this.configPanel.getForm().getFieldValues();

        if(this.record.data.config.name !== this.record.data.text) {
            this.record.set("text", this.record.data.config.name);
        }

        this.window.close();
    }
});