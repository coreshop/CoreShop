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

pimcore.registerNS('coreshop.order.resource');
coreshop.order.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.resource.global.addStore('coreshop_cart_price_rules', 'coreshop/cart_price_rules');

        /*pimcore.globalmanager.add('coreshop_order_states', new Ext.data.JsonStore({
            data: this.settings.orderStates,
            fields: ['name', 'label', 'color'],
            idProperty: 'name'
        }));*/
    }
});

new coreshop.order.resource();