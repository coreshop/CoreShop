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

pimcore.registerNS('coreshop.store.panel');
coreshop.store.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_stores_panel',
    storeId: 'coreshop_stores',
    iconCls: 'coreshop_icon_store',
    type: 'coreshop_stores',

    url: {
        add: '/admin/coreshop/stores/add',
        delete: '/admin/coreshop/stores/delete',
        get: '/admin/coreshop/stores/get',
        list: '/admin/coreshop/stores/list'
    },

    getItemClass: function() {
        return coreshop.store.item;
    }
});
