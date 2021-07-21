/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.index.item');

coreshop.index.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_indexes',

    routing: {
        save: 'coreshop_index_save'
    },

    getPanel: function () {
        return new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls: this.iconCls,
            buttons: [{
                text: t('save'),
                iconCls: 'pimcore_icon_apply',
                handler: this.save.bind(this)
            }],
            items: this.getItems()
        });
    },

    getItems: function () {
        var fields = this.getIndexFields();
        var settings = this.getSettings();

        return [
            settings,
            fields
        ];
    },

    getSettings: function () {
        this.indexWorkerSettings = new Ext.form.Panel({});

        this.formPanel = new Ext.panel.Panel({
            iconCls: 'coreshop_icon_settings',
            title: t('settings'),
            bodyStyle: 'padding:20px 5px 20px 5px;',
            border: false,
            region: 'center',
            autoScroll: true,
            forceLayout: true,
            defaults: {
                forceLayout: true
            },
            items: [
                {
                    xtype: 'form',
                    items: [
                        {
                            xtype: 'fieldset',
                            autoHeight: true,
                            border: false,
                            labelWidth: 350,
                            defaultType: 'textfield',
                            defaults: {width: '100%'},
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: t('name'),
                                    name: 'name',
                                    value: this.data.name,
                                    regex: /^[a-z0-9]+$/i
                                },
                                {
                                    xtype: 'combo',
                                    fieldLabel: t('class'),
                                    name: 'class',
                                    displayField: 'name',
                                    valueField: 'name',
                                    store: pimcore.globalmanager.get('coreshop_index_classes'),
                                    value: this.data.class,
                                    queryMode: 'local',
                                    forceSelection: true
                                },
                                {
                                    xtype: 'combo',
                                    fieldLabel: t('coreshop_indexes_type'),
                                    typeAhead: true,
                                    value: this.data.worker,
                                    mode: 'local',
                                    listWidth: 100,
                                    store: {
                                        type: 'coreshop_index_types'
                                    },
                                    displayField: 'name',
                                    valueField: 'name',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    name: 'worker',
                                    listeners: {
                                        change: function (combo, value) {
                                            this.getIndexWorkerConfig(value);
                                        }.bind(this)
                                    }
                                },
                                {
                                    xtype: 'checkbox',
                                    fieldLabel: t('coreshop_index_last_version'),
                                    name: 'indexLastVersion',
                                    checked: this.data.indexLastVersion
                                }
                            ]
                        }
                    ]
                },
                this.indexWorkerSettings
            ]
        });

        if (this.data.worker) {
            this.getIndexWorkerConfig(this.data.worker);
        }

        return this.formPanel;
    },

    getIndexFields: function () {
        this.fieldsPanel = new coreshop.index.fields(this.data, this.data.class);
        this.indexFields = new Ext.panel.Panel({
            iconCls: 'coreshop_icon_indexes_fields',
            title: t('coreshop_indexes_fields'),
            border: false,
            layout: 'fit',
            region: 'center',
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

    getIndexWorkerConfig: function (worker) {
        if (this.indexWorkerSettings) {
            this.indexWorkerSettings.removeAll();

            if (coreshop.index.worker[worker] !== undefined) {
                this.workerSettings = new coreshop.index.worker[worker](this);
                this.indexWorkerSettings.add(this.workerSettings.getForm(this.data.configuration));
            }
            else {
                this.workerSettings = null;
            }

            if (this.indexWorkerSettings.items.items.length === 0) {
                this.indexWorkerSettings.hide();
            }
            else {
                this.indexWorkerSettings.show();
            }
        }
    },

    getSaveData: function () {
        var saveData = this.formPanel.down("form").getForm().getFieldValues();

        if (this.workerSettings && Ext.isFunction(this.workerSettings.getData)) {
            saveData['configuration'] = this.workerSettings.getData();
        }
        else {
            saveData['configuration'] = this.indexWorkerSettings.getForm().getFieldValues();
        }
        saveData['columns'] = this.fieldsPanel.getData();

        return saveData;
    },

    postSave: function (res) {
        if (res.success) {
            if (res.data.class) {
                this.fieldsPanel.setClass(res.data.class);
                this.fieldsPanel.reload();
            }
        }
    },

    isValid: function () {
        return this.formPanel.down("form").isValid();
    }
});
