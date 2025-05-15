/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.zone.item');
coreshop.zone.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_zone',

    routing: {
        save: 'coreshop_zone_save'
    },

    getItems: function () {
        return [this.getFormPanel()];
    },

    getFormPanel: function () {

        this.formPanel = new Ext.form.Panel({
            bodyStyle: 'padding:20px 5px 20px 5px;',
            border: false,
            region: 'center',
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
                    xtype: 'fieldset',
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 300},
                    items: [
                        {
                            fieldLabel: t('name'),
                            name: 'name',
                            value: this.data.name
                        },
                        {
                            xtype: 'checkbox',
                            fieldLabel: t('active'),
                            name: 'active',
                            checked: this.data.active
                        }
                    ]
                }
            ]
        });

        return this.formPanel;
    },

    getSaveData: function () {
        var values = this.formPanel.getForm().getFieldValues();

        if (!values['active']) {
            delete values['active'];
        }

        return values;
    }
});
