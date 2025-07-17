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

pimcore.registerNS('coreshop.address.resource');
coreshop.address.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStoreWithRoute('coreshop_zones', 'coreshop_zone_list', [
            {name: 'id'},
            {name: 'name'},
            {name: 'active'}
        ]);
        coreshop.global.addStoreWithRoute('coreshop_countries', 'coreshop_country_list', null, 'name');
        coreshop.global.addStoreWithRoute('coreshop_address_identifier', 'coreshop_address_identifier_list', null, 'name');
        coreshop.global.addStoreWithRoute('coreshop_states', 'coreshop_state_list');

        coreshop.broker.fireEvent('resource.register', 'coreshop.address', this);
    },

    openResource: function (item) {
        if (item === 'country') {
            this.openCountryResource();
        } else if (item === 'state') {
            this.openStateResource();
        } else if (item === 'zone') {
            this.openZoneResource();
        }
    },

    openCountryResource: function () {
        try {
            pimcore.globalmanager.get('coreshop_countries_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_countries_panel', new coreshop.country.panel());
        }
    },

    openZoneResource: function () {
        try {
            pimcore.globalmanager.get('coreshop_zones_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_zones_panel', new coreshop.zone.panel());
        }
    },

    openStateResource: function () {
        try {
            pimcore.globalmanager.get('coreshop_states_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_states_panel', new coreshop.state.panel());
        }
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.address.resource();
});
