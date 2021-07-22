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

pimcore.registerNS('coreshop.provider.panel');
coreshop.provider.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_payment_providers_panel',
    storeId: 'coreshop_payment_providers',
    iconCls: 'coreshop_icon_payment_provider',
    type: 'coreshop_payment_provider',

    url: {
        add: '/admin/coreshop/payment_providers/add',
        delete: '/admin/coreshop/payment_providers/delete',
        get: '/admin/coreshop/payment_providers/get',
        list: '/admin/coreshop/payment_providers/list',
        config: '/admin/coreshop/payment_providers/get-config'
    },

    factoryTypes: null,

    /**
     * constructor
     */
    initialize: function () {
        this.getConfig();

        this.panels = [];

        this.store = new Ext.data.Store({
            restful: false,
            proxy: new Ext.data.HttpProxy({
                url: this.url.list
            }),
            reader: new Ext.data.JsonReader({
                rootProperty: 'data'
            }, [
                {name: 'id'},
                {name: 'identifier'}
            ]),
            autoload: true
        });
    },

    getTitleText: function () {
        return this.data.identifier;
    },

    getConfig: function () {
        this.factoryTypes = new Ext.data.ArrayStore({
            data: [],
            expandedData: true
        });

        pimcore.globalmanager.add('coreshop_payment_provider_factories', this.factoryTypes);

        Ext.Ajax.request({
            url: this.url.config,
            method: 'get',
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);

                    this.factoryTypes.loadData(res.factories);

                    this.getLayout();
                } catch (e) {
                    //pimcore.helpers.showNotification(t('error'), t('coreshop_save_error'), 'error');
                }
            }.bind(this)
        });
    },

    getItemClass: function () {
        return coreshop.payment.provider.item;
    },

    getGridConfiguration: function () {
        return {
            store: this.store
        };
    },

    getDefaultGridDisplayColumnName: function() {
        return 'identifier';
    },

    prepareAdd: function (object) {
        object['identifier'] = object.name;

        return object;
    },

    getItemClass: function() {
        return coreshop.provider.item;
    }
});
