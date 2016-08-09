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

pimcore.registerNS('pimcore.plugin.coreshop.states.item');
pimcore.plugin.coreshop.states.item = Class.create(pimcore.plugin.coreshop.abstract.item, {

    iconCls : 'coreshop_icon_state',

    url : {
        save : '/plugin/CoreShop/admin_state/save'
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
                    labelWidth: 350,
                    defaultType: 'textfield',
                    defaults: { width: 300 },
                    items :[
                        {
                            fieldLabel: t('coreshop_state_name'),
                            name: 'name',
                            value: this.data.name
                        },
                        {
                            fieldLabel: t('coreshop_state_isoCode'),
                            name: 'isoCode',
                            value: this.data.isoCode
                        },
                        {
                            xtype : 'checkbox',
                            fieldLabel: t('coreshop_state_active'),
                            name: 'active',
                            checked: this.data.active === '1'
                        },
                        {
                            xtype:'combo',
                            fieldLabel:t('coreshop_state_country'),
                            typeAhead:true,
                            value:this.data.countryId,
                            mode:'local',
                            listWidth:100,
                            store:pimcore.globalmanager.get('coreshop_countries'),
                            displayField:'name',
                            valueField:'id',
                            forceSelection:true,
                            triggerAction:'all',
                            name:'zoneId',
                            listeners: {
                                change: function () {
                                    this.forceReloadOnSave = true;
                                }.bind(this),
                                select: function () {
                                    this.forceReloadOnSave = true;
                                }.bind(this)
                            }
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
