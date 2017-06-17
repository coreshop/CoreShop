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

pimcore.registerNS('coreshop.taxation.resource');
coreshop.taxation.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.resource.global.addStore('coreshop_tax_rates', 'coreshop/tax_rates', [
            {name: 'id'},
            {name: 'name'},
            {name: 'rate'}
        ]);
        coreshop.resource.global.addStore('coreshop_taxrulegroups', 'coreshop/tax_rule_groups');

        pimcore.globalmanager.get('coreshop_tax_rates').load();
    }
});

new coreshop.taxation.resource();