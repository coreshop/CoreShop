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

pimcore.registerNS('pimcore.plugin.coreshop.customergroups.item');
pimcore.plugin.coreshop.customergroups.item = Class.create(pimcore.plugin.coreshop.abstract.item, {

    iconCls : 'coreshop_icon_customer_groups',

    url : {
        save : '/plugin/CoreShop/admin_customer-group/save'
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
                    items :[
                        {
                            name: 'name',
                            fieldLabel: t('name'),
                            width: 400,
                            value: data.name
                        },
                        {
                            xtype: 'numberfield',
                            name: 'discount',
                            fieldLabel: t('coreshop_customer_group_discount'),
                            width: 400,
                            value: data.discount,
                            decimalPrecision : 2,
                            step : 1
                        }
                    ]
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
