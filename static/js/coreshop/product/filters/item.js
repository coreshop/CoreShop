/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.filters.item');

pimcore.plugin.coreshop.filters.item = Class.create(pimcore.plugin.coreshop.abstract.item, {

    iconCls : 'coreshop_icon_product_filters',

    url : {
        save : '/plugin/CoreShop/admin_filter/save'
    },

    indexFieldsStore : null,

    getPanel: function () {
        panel = new Ext.TabPanel({
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

        return panel;
    },

    getItems : function () {
        this.conditions = new pimcore.plugin.coreshop.filters.condition(this, this.parentPanel.conditions, 'preconditions');
        this.filters = new pimcore.plugin.coreshop.filters.condition(this, this.parentPanel.conditions, 'filters');
        this.similarities = new pimcore.plugin.coreshop.filters.similarity(this, this.parentPanel.similarities);

        var items = [
            this.getSettings(),
            this.conditions.getLayout(),
            this.filters.getLayout(),
            this.similarities.getLayout()
        ];

        // add saved conditions
        if (this.data.preConditions)
        {
            Ext.each(this.data.preConditions, function (condition) {
                this.conditions.addCondition(condition.type, condition);
            }.bind(this));
        }

        if (this.data.filters) {
            Ext.each(this.data.filters, function (condition) {
                this.filters.addCondition(condition.type, condition);
            }.bind(this));
        }

        if (this.data.similarities) {
            Ext.each(this.data.similarities, function (similarity) {
                this.similarities.addSimilarity(similarity.type, similarity);
            }.bind(this));
        }

        this.indexCombo.setValue(this.data.index);

        if (!this.data.index) {
            this.conditions.disable();
            this.filters.disable();
        }

        return items;
    },

    getFieldsForIndex : function (forceReload) {
        if (!this.indexFieldsStore) {
            var proxy = new Ext.data.HttpProxy({
                url : '/plugin/CoreShop/admin_filter/get-fields-for-index'
            });

            var reader = new Ext.data.JsonReader({}, [
                { name:'name' }
            ]);

            this.indexFieldsStore = new Ext.data.Store({
                restful:    false,
                proxy:      proxy,
                reader:     reader,
                autoload:   true
            });
        }

        if (forceReload || !this.indexFieldsStore.isLoaded()) {
            this.indexFieldsStore.proxy.extraParams = { index : this.indexCombo.getValue() };
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
            fieldLabel: t('coreshop_product_filters_index'),
            typeAhead: true,
            listWidth: 100,
            width : 250,
            store: pimcore.globalmanager.get('coreshop_indexes'),
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            name:'index',
            value : data.index,
            listeners: {
                change : function (combo, value) {
                    if (value) {
                        this.conditions.enable();
                        this.filters.enable();

                        this.getFieldsForIndex();
                    } else {
                        this.conditions.disable();
                        this.filters.disable();
                    }
                }.bind(this)
            }
        });

        this.settingsForm = Ext.create('Ext.form.Panel', {
            iconCls: 'coreshop_price_rule_settings',
            title: t('settings'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border:false,
            items: [{
                xtype: 'textfield',
                name: 'name',
                fieldLabel: t('name'),
                width: 250,
                value: data.name
            }, {
                xtype : 'numberfield',
                fieldLabel:t('coreshop_product_filters_resultsPerPage'),
                name:'resultsPerPage',
                value : data.resultsPerPage,
                minValue : 1,
                decimalPrecision : 0,
                step : 1
            }, {
                xtype: 'combo',
                fieldLabel: t('coreshop_product_filters_order'),
                name: 'order',
                value: data.order,
                width: 250,
                store: [['desc', t('coreshop_product_filters_order_desc')], ['asc', t('coreshop_product_filters_order_asc')]],
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local'
            }, {
                xtype: 'textfield',
                name: 'orderKey',
                fieldLabel: t('coreshop_product_filters_orderKey'),
                width: 250,
                value: data.orderKey
            }, this.indexCombo]
        });

        return this.settingsForm;
    },

    getSaveData : function () {
        var saveData = {};

        // general settings
        saveData['settings'] = this.settingsForm.getForm().getFieldValues();
        saveData['conditions'] = this.conditions.getData();
        saveData['filters'] = this.filters.getData();
        saveData['similarities'] = this.similarities.getData();

        return {
            data : Ext.encode(saveData)
        };
    }
});
