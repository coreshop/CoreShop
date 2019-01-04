/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.currency.panel');
coreshop.currency.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_currencies_panel',
    storeId: 'coreshop_currencies',
    iconCls: 'coreshop_icon_currency',
    type: 'coreshop_currencies',

    url: {
        add: '/admin/coreshop/currencies/add',
        delete: '/admin/coreshop/currencies/delete',
        get: '/admin/coreshop/currencies/get',
        list: '/admin/coreshop/currencies/list'
    },

    getItemClass: function() {
        return coreshop.currency.item;
    }
});
