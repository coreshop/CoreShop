/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.report.abstractStore');
coreshop.report.abstractStore = Class.create(coreshop.report.abstract, {
    reportType: 'abstractStoreReport',

    getFilterFields: function ($super) {
        var me = this,
            store = pimcore.globalmanager.get('coreshop_stores').valueOf(),
            filter = $super();

        filter.splice(0, 0, {
            xtype: 'combo',
            fieldLabel: null,
            listWidth: 100,
            width: 200,
            store: store,
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect: false,
            triggerAction: 'all',
            name: 'store',
            queryMode: 'remote',
            maxHeight: 400,
            delimiter: false,
            listeners: {
                afterrender: function () {
                    var first;
                    if (this.store.isLoaded()) {
                        first = this.store.getAt(0);
                        this.setValue(first);
                    } else {
                        this.store.load();
                        this.store.on('load', function (store, records, options) {
                            first = store.getAt(0);
                            this.setValue(first);
                        }.bind(this));
                    }
                },
                change: function (combo, value) {
                    this.getStoreField().setValue(value);
                    this.filter();
                }.bind(this)
            }
        });

        return filter;
    },

    getFilterParams: function ($super) {
        var params = $super();
        params['store'] = this.getStoreField().getValue();

        return params;
    }
});

