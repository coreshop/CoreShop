/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.manufacturers.item');
pimcore.plugin.coreshop.manufacturers.item = Class.create(pimcore.plugin.coreshop.abstract.item, {

    iconCls : 'coreshop_icon_manufacturers',

    url : {
        save : '/plugin/CoreShop/admin_manufacturer/save'
    },

    getItems : function () {
        return [this.getFormPanel()];
    },

    getTitleName : function () {
        return this.data.name;
    },

    getFormPanel : function ()
    {
        var data = this.data;

        var items = [
            {
                name: 'name',
                fieldLabel: t('name'),
                width: 400,
                value: data.name
            },
            {
                fieldLabel: t('coreshop_manufacturer_image'),
                name: 'image',
                cls: 'input_drop_target',
                value: this.data.image ? this.data.image : null,
                width: 300,
                xtype: 'textfield',
                listeners: {
                    render: function (el) {
                        new Ext.dd.DropZone(el.getEl(), {
                            reference: this,
                            ddGroup: 'element',
                            getTargetFromEvent: function (e) {
                                return this.getEl();
                            }.bind(el),

                            onNodeOver : function (target, dd, e, data) {
                                data = data.records[0].data;

                                if (data.elementType == 'asset') {
                                    return Ext.dd.DropZone.prototype.dropAllowed;
                                }

                                return Ext.dd.DropZone.prototype.dropNotAllowed;
                            },

                            onNodeDrop : function (target, dd, e, data) {
                                data = data.records[0].data;

                                if (data.elementType == 'asset') {
                                    this.setValue(data.id);
                                    return true;
                                }

                                return false;
                            }.bind(el)
                        });
                    }
                }
            }
        ];

        if (this.getMultishopSettings()) {
            items.push(this.getMultishopSettings());
        }

        this.formPanel = new Ext.form.Panel({
            bodyStyle:'padding:20px 5px 20px 5px;',
            border: false,
            region : 'center',
            autoScroll: true,
            forceLayout: true,
            defaults: {
                forceLayout: true
            },
            buttons: [
                {
                    text: t('save'),
                    handler: this.save.bind(this),
                    iconCls: 'pimcore_icon_apply'
                }
            ],
            items: [
                {
                    xtype:'fieldset',
                    autoHeight:true,
                    labelWidth: 350,
                    defaultType: 'textfield',
                    defaults: { width: '100%' },
                    items : items
                }
            ]
        });

        return this.formPanel;
    },

    getSaveData : function () {
        return {
            data: Ext.encode(this.formPanel.getForm().getFieldValues())
        };
    }
});
