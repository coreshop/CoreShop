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

pimcore.registerNS('coreshop.store.item');
coreshop.store.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_store',

    routing: {
        save: 'coreshop_store_save'
    },

    getItems: function () {
        return [this.getFormPanel()];
    },

    getFormPanel: function () {
        this.store = new Ext.data.Store({
            restful: false,
            idProperty: 'id',
            remoteSort: true,
            proxy: {
                type: 'ajax',
                url: Routing.generate('coreshop_store_listSites'),
                reader: {
                    type: 'json'
                }
            }
        });

        this.store.load();

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
                            fieldLabel: t('coreshop_store_site'),
                            xtype: 'combo',
                            name: 'siteId',
                            width: 400,
                            store: this.store,
                            displayField: 'name',
                            valueField: 'id',
                            triggerAction: 'all',
                            typeAhead: false,
                            editable: false,
                            forceSelection: true,
                            queryMode: 'local',
                            value: this.data.siteId
                        },
                        {
                            fieldLabel: t('coreshop_store_template'),
                            name: 'template',
                            value: this.data.template
                        },
                        {
                            xtype: 'coreshop.currency',
                            value: this.data.currency
                        },
                    ]
                }
            ]
        });

        return this.formPanel;
    },

    getSaveData: function () {
        var values = this.formPanel.getForm().getFieldValues();

        return values;
    }
});
