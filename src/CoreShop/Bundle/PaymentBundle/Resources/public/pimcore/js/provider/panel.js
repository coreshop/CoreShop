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

pimcore.registerNS('coreshop.provider.panel');
coreshop.provider.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_payment_providers_panel',
    storeId: 'coreshop_payment_providers',
    iconCls: 'coreshop_icon_payment_provider',
    type: 'coreshop_payment_provider',

    routing: {
        add: 'coreshop_payment_provider_add',
        delete: 'coreshop_payment_provider_delete',
        get: 'coreshop_payment_provider_get',
        list: 'coreshop_payment_provider_list',
        config: 'coreshop_admin_payment_provider_config'
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
                url: Routing.generate(this.routing.list)
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
            url: Routing.generate(this.routing.config),
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
