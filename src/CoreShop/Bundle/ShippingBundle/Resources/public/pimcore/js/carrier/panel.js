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

pimcore.registerNS('coreshop.carrier.panel');
coreshop.carrier.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_carriers_panel',
    storeId: 'coreshop_carriers',
    iconCls: 'coreshop_icon_carriers',
    type: 'coreshop_carriers',

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
