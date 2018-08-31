/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.address.resource');
coreshop.address.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStore('coreshop_zones', 'coreshop/zones', [
            {name: 'id'},
            {name: 'name'},
            {name: 'active'}
        ]);
        coreshop.global.addStore('coreshop_countries', 'coreshop/countries', null, 'name');
        coreshop.global.addStore('coreshop_countries_active', 'coreshop/countries', null, 'name');
        coreshop.global.addStore('coreshop_states', 'coreshop/states');

        pimcore.globalmanager.get('coreshop_countries_active').addFilter({
            property: 'active',
            value: true
        });

        pimcore.globalmanager.get('coreshop_countries').load();
        pimcore.globalmanager.get('coreshop_countries_active').load();
        pimcore.globalmanager.get('coreshop_states').load();
        pimcore.globalmanager.get('coreshop_zones').load();

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