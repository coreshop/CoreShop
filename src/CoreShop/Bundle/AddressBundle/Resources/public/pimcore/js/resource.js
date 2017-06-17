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
        coreshop.resource.global.addStore('coreshop_zones', 'coreshop/zones', [
            {name: 'id'},
            {name: 'name'},
            {name: 'active'}
        ]);
        coreshop.resource.global.addStore('coreshop_countries', 'coreshop/countries');
        coreshop.resource.global.addStore('coreshop_states', 'coreshop/states');

        pimcore.globalmanager.get('coreshop_countries').load();
        pimcore.globalmanager.get('coreshop_states').load();
        pimcore.globalmanager.get('coreshop_zones').load();
    }
});

new coreshop.address.resource();