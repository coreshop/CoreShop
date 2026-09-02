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

pimcore.registerNS('coreshop.index.resource');
coreshop.index.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStoreWithRoute('coreshop_indexes', 'coreshop_index_list');
        coreshop.global.addStoreWithRoute('coreshop_filters', 'coreshop_filter_list');
        coreshop.global.addStoreWithUrl('coreshop_index_types', Routing.generate('coreshop_index_getTypes'));

        coreshop.broker.fireEvent('resource.register', 'coreshop.index', this);

        if (coreshop.menu.coreshop.index) {
            new coreshop.menu.coreshop.index();
        }
    },

    openResource: function(item) {
        if (item === 'index') {
            this.openIndex();
        } else if(item === 'filter') {
            this.openFilter();
        }
    },

    openIndex: function() {
        try {
            pimcore.globalmanager.get('coreshop_indexes_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_indexes_panel', new coreshop.index.panel());
        }
    },

    openFilter: function() {
        try {
            pimcore.globalmanager.get('coreshop_filters_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_filters_panel', new coreshop.filter.panel());
        }
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.index.resource();
});
