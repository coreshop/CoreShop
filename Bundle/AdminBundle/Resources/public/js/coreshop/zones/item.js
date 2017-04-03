/**
 * CoreShop
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

pimcore.registerNS('pimcore.plugin.coreshop.zones.item');

pimcore.plugin.coreshop.zones.item = Class.create(pimcore.plugin.coreshop.abstract.item, {

    iconCls : 'coreshop_icon_zone',

    url : {
        save : '/admin/CoreShop/zones/save'
    },

    getItems : function () {
        return [this.getFormPanel()];
    },

    getFormPanel : function () {

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
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: { width: 300 },
                    items :[
                        {
                            fieldLabel: t('coreshop_zone_name'),
                            name: 'name',
                            value: this.data.name
                        },
                        {
                            xtype : 'checkbox',
                            fieldLabel: t('coreshop_zone_active'),
                            name: 'active',
                            checked: this.data.active
                        }
                    ]
                }
            ]
        });

        return this.formPanel;
    },

    getSaveData : function () {
        var values = this.formPanel.getForm().getFieldValues();

        if (!values['active']) {
            delete values['active'];
        }

        return values;
    }
});
