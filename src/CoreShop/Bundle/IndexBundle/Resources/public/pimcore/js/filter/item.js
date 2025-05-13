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

pimcore.registerNS('coreshop.filter.item');

coreshop.filter.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_filters',

    routing: {
        save: 'coreshop_filter_save',
        clone: 'coreshop_filter_clone',
    },

    indexFieldsStore: null,

    getPanel: function () {
        panel = new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls: this.iconCls,
            buttons: [{
                text: t('clone'),
                iconCls: 'pimcore_icon_clone',
                handler: this.clone.bind(this)
            }, {
                text: t('save'),
                iconCls: 'pimcore_icon_apply',
                handler: this.save.bind(this)
            }],
            items: this.getItems()
        });

        return panel;
    },

    getItems: function () {
        this.preConditions = new coreshop.filter.condition(this, this.parentPanel.pre_conditions, 'preConditions', 'pre_conditions');
        this.conditions = new coreshop.filter.condition(this, this.parentPanel.user_conditions, 'conditions');
        //this.similarities = new coreshop.filter.similarity(this, this.parentPanel.similarities);

        var items = [
            this.getSettings(),
            this.preConditions.getLayout(),
            this.conditions.getLayout()
            //this.similarities.getLayout()
        ];

        // add saved conditions
        if (this.data.preConditions) {
            Ext.each(this.data.preConditions, function (condition, index) {
                this.preConditions.addCondition(condition.type, condition, index, false);
            }.bind(this));
        }

        if (this.data.conditions) {
            Ext.each(this.data.conditions, function (condition, index) {
                this.conditions.addCondition(condition.type, condition, index, false);
            }.bind(this));
        }

        /*if (this.data.similarities) {
         Ext.each(this.data.similarities, function (similarity) {
         this.similarities.addSimilarity(similarity.type, similarity);
         }.bind(this));
         }*/

        this.indexCombo.setValue(this.data.index);

        if (!this.data.index) {
            this.preConditions.disable();
            this.conditions.disable();
        }

        return items;
    },

    getFieldsForIndex: function (forceReload) {
        if (!this.indexFieldsStore) {
            var proxy = new Ext.data.HttpProxy({
                url: Routing.generate('coreshop_filter_getFieldsForIndex')
            });

            var reader = new Ext.data.JsonReader({}, [
                {name: 'name'}
            ]);

            this.indexFieldsStore = new Ext.data.Store({
                restful: false,
                proxy: proxy,
                reader: reader,
                autoload: true
            });
        }

        if (forceReload || !this.indexFieldsStore.isLoaded()) {
            this.indexFieldsStore.proxy.extraParams = {index: this.indexCombo.getValue()};
            this.indexFieldsStore.load({
                params: {
                    index: this.indexCombo.getValue()
                }
            });
        }

        return this.indexFieldsStore;
    },

    getSettings: function () {
        var data = this.data;

        this.indexCombo = Ext.create({
            xtype: 'combo',
            fieldLabel: t('coreshop_filters_index'),
            typeAhead: true,
            listWidth: 100,
            width: 250,
            store: {
                type: 'coreshop_indexes'
            },
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            name: 'index',
            value: data.index,
            listeners: {
                change: function (combo, value) {
                    if (value) {
                        this.conditions.enable();
                        this.preConditions.enable();

                        this.getFieldsForIndex();
                    } else {
                        this.conditions.disable();
                        this.preConditions.disable();
                    }
                }.bind(this)
            }
        });

        this.settingsForm = Ext.create('Ext.form.Panel', {
            iconCls: 'coreshop_icon_settings',
            title: t('settings'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border: false,
            items: [{
                xtype: 'textfield',
                name: 'name',
                fieldLabel: t('name'),
                width: 250,
                value: data.name
            }, this.indexCombo, {
                xtype: 'combo',
                fieldLabel: t('coreshop_filters_order'),
                name: 'orderDirection',
                value: data.orderDirection,
                width: 250,
                store: [['desc', t('coreshop_filters_order_desc')], ['asc', t('coreshop_filters_order_asc')]],
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local'
            }, {
                xtype: 'textfield',
                name: 'orderKey',
                fieldLabel: t('coreshop_filters_orderKey'),
                width: 250,
                value: data.orderKey
            }, {
                xtype: 'numberfield',
                fieldLabel: t('coreshop_filters_resultsPerPage'),
                name: 'resultsPerPage',
                value: data.resultsPerPage,
                minValue: 1,
                decimalPrecision: 0,
                step: 1
            }]
        });

        return this.settingsForm;
    },

    getSaveData: function () {
        var saveData = this.settingsForm.getForm().getFieldValues();

        saveData['preConditions'] = this.preConditions.getData();
        saveData['conditions'] = this.conditions.getData();
        //saveData['similarities'] = this.similarities.getData();

        return saveData;
    }
});
