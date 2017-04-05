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

pimcore.registerNS('pimcore.plugin.coreshop.indexes.item');

pimcore.plugin.coreshop.indexes.item = Class.create(pimcore.plugin.coreshop.abstract.item, {

    iconCls : 'coreshop_icon_indexes',

    url : {
        save : '/admin/CoreShop/indices/save'
    },

    getPanel: function () {
        return new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls : this.iconCls,
            buttons: [{
                text: t('save'),
                iconCls: 'pimcore_icon_apply',
                handler: this.save.bind(this)
            }],
            items: this.getItems()
        });
    },

    getItems : function () {
        return [
            this.getSettings(),
            this.getIndexFields()
        ];
    },

    getSettings : function ()
    {
        this.indexTypeSettings = new Ext.form.Panel({});

        if(this.data.type) {
            this.getIndexTypeConfig(this.data.type);
        }

        this.formPanel = new Ext.panel.Panel({
            iconCls: 'coreshop_icon_settings',
            title: t('settings'),
            bodyStyle:'padding:20px 5px 20px 5px;',
            border: false,
            region : 'center',
            autoScroll: true,
            forceLayout: true,
            defaults: {
                forceLayout: true
            },
            items : [
                {
                    xtype : 'form',
                    items: [
                        {
                            xtype:'fieldset',
                            autoHeight:true,
                            labelWidth: 350,
                            defaultType: 'textfield',
                            defaults: { width: '100%' },
                            items :[
                                {
                                    xtype : 'textfield',
                                    fieldLabel:t('coreshop_indexes_name'),
                                    name : 'name',
                                    value : this.data.name,
                                    regex: /^[a-z0-9]+$/i
                                },
                                {
                                    xtype:'combo',
                                    fieldLabel:t('coreshop_indexes_type'),
                                    typeAhead:true,
                                    value:this.data.worker,
                                    mode:'local',
                                    listWidth:100,
                                    store: this.parentPanel.typesStore,
                                    displayField:'name',
                                    valueField:'name',
                                    forceSelection:true,
                                    triggerAction:'all',
                                    name:'worker',
                                    listeners : {
                                        change: function (combo, value) {
                                            this.getIndexTypeConfig(value);
                                        }.bind(this)
                                    }
                                }
                            ]
                        }
                    ]
                },
                this.indexTypeSettings
            ]
        });

        return this.formPanel;
    },

    getIndexFields : function () {
        this.fieldsPanel = new pimcore.plugin.coreshop.indexes.fields(this.data, this.parentPanel.classId);

        this.indexFields = new Ext.panel.Panel({
            iconCls: 'coreshop_icon_indexes_fields',
            title: t('coreshop_indexes_fields'),
            border: false,
            layout: 'fit',
            region : 'center',
            autoScroll: true,
            forceLayout: true,
            defaults: {
                forceLayout: true
            },
            items: [
                this.fieldsPanel.getLayout()
            ]
        });

        return this.indexFields;
    },

    getIndexTypeConfig : function(worker) {
        if (this.indexTypeSettings) {
            this.indexTypeSettings.removeAll();

            if (pimcore.plugin.coreshop.indexes.type[worker] !== undefined) {
                var workerSettings = new pimcore.plugin.coreshop.indexes.type[worker]();

                this.indexTypeSettings.add(workerSettings.getFields(this.data.config));
            }

            if(this.indexTypeSettings.items.items.length === 0) {
                this.indexTypeSettings.hide();
            }
            else {
                this.indexTypeSettings.show();
            }
        }
    },

    getSaveData : function () {
        var saveData = this.formPanel.down("form").getForm().getFieldValues();

        saveData['configuration'] = this.indexTypeSettings.getForm().getFieldValues();
        saveData['columns'] = this.fieldsPanel.getData();

        return saveData;
    },

    isValid : function() {
        return this.formPanel.down("form").isValid();
    }
});
