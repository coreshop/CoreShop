/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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

    url: {
        add: '/admin/coreshop/carriers/add',
        delete: '/admin/coreshop/carriers/delete',
        get: '/admin/coreshop/carriers/get',
        list: '/admin/coreshop/carriers/list',
        config: '/admin/coreshop/carriers/get-config'
    },

    /**
     * constructor
     */
    initialize: function () {
        this.getConfig();

        this.panels = [];
    },

    getConfig: function () {
        this.taxStrategyStore = new Ext.data.JsonStore({
            data: []
        });

        pimcore.globalmanager.add('coreshop_shipping_tax_strategies', this.taxStrategyStore);

        Ext.Ajax.request({
            url: this.url.config,
            method: 'get',
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);

                    res.taxStrategies.forEach(element => element.label = t(element.label));

                    this.taxStrategyStore.loadData(res.taxStrategies);

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
