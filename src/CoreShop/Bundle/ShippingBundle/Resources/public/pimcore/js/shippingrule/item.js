/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.shippingrule.item');

coreshop.shippingrule.item = Class.create(coreshop.rules.item, {

    iconCls: 'coreshop_icon_carrier_shipping_rule',

    routing: {
        save: 'coreshop_shipping_rule_save'
    },

    getPanel: function () {
        var items = this.getItems();

        //items.push(this.getUsedByPanel()); TODO!!

        this.panel = new Ext.TabPanel({
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
            items: items
        });

        return this.panel;
    },

    getSettings: function () {
        var data = this.data;

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
            }, {
                xtype: 'checkbox',
                name: 'active',
                fieldLabel: t('active'),
                checked: data.active
            }]
        });

        return this.settingsForm;
    },
    //
    // getUsedByPanel: function () {
    //     this.store = new Ext.data.JsonStore({
    //         fields: [
    //             'id',
    //             'name'
    //         ],
    //         proxy: {
    //             type: 'ajax',
    //             url: '/admin/coreshop/carrier-shipping-rule/get-used-by-carriers',
    //             reader: {
    //                 type: 'json',
    //                 rootProperty: 'carriers'
    //             },
    //             extraParams: {
    //                 id: this.data.id
    //             }
    //         }
    //     });
    //
    //     var columns = [
    //         {
    //             text: t('id'),
    //             dataIndex: 'id'
    //         },
    //         {
    //             text: t('coreshop_carrier'),
    //             dataIndex: 'name',
    //             flex: 1
    //         }
    //     ];
    //
    //     this.grid = Ext.create('Ext.grid.Panel', {
    //         title: t('coreshop_carriers'),
    //         iconCls: 'coreshop_icon_carriers',
    //         store: this.store,
    //         columns: columns,
    //         region: 'center'
    //     });
    //
    //     this.store.load();
    //
    //     return this.grid;
    // },

    getActionContainerClass: function () {
        return coreshop.shippingrule.action;
    },

    getConditionContainerClass: function () {
        return coreshop.shippingrule.condition;
    }
});
