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

pimcore.registerNS('coreshop.order.quote.list');
coreshop.order.quote.list = Class.create(coreshop.order.quote.list, {
    storeRenderer: function (val) {
        var stores = pimcore.globalmanager.get('coreshop_stores');
        var store = stores.getById('id', String(val));
        if (store) {
            return store.get('name');
        }

        return null;
    }
});

