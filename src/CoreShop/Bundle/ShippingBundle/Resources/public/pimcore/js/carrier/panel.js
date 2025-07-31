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

pimcore.registerNS('coreshop.carrier.panel');
coreshop.carrier.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_carriers_panel',
    storeId: 'coreshop_carriers',
    iconCls: 'coreshop_icon_carriers',
    type: 'coreshop_carriers',
    permission: 'coreshop_permission_carrier',

    routing: {
        add: 'coreshop_carrier_add',
        delete: 'coreshop_carrier_delete',
        get: 'coreshop_carrier_get',
        list: 'coreshop_carrier_list',
        config: 'coreshop_carrier_getConfig'
    },

    /**
     * constructor
     */
    initialize: function () {
        this.getConfig();

        this.panels = [];
    },

    getConfig: function () {
        this.taxCalculationStrategyStore = new Ext.data.JsonStore({
            data: []
        });

        pimcore.globalmanager.add('coreshop_shipping_tax_calculation_strategies', this.taxCalculationStrategyStore);

        Ext.Ajax.request({
            url: Routing.generate(this.routing.config),
            method: 'get',
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);

                    res.taxCalculationStrategies.forEach(element => element.label = t(element.label));

                    this.taxCalculationStrategyStore.loadData(res.taxCalculationStrategies);

                    // create layout
                    this.getLayout();
                } catch (e) {
                    //pimcore.helpers.showNotification(t('error'), t('coreshop_save_error'), 'error');
                }
            }.bind(this)
        });
    },

    getItemClass: function() {
        return coreshop.carrier.item;
    },

    getDefaultGridDisplayColumnName: function() {
        return 'identifier';
    },

    prepareAdd: function (object) {
        object['identifier'] = object.name;
        return object;
    }
});
