/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.zone.panel');
coreshop.zone.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_zones_panel',
    storeId: 'coreshop_zones',
    iconCls: 'coreshop_icon_zone',
    type: 'coreshop_zones',

    url: {
        add: '/admin/coreshop/zones/add',
        delete: '/admin/coreshop/zones/delete',
        get: '/admin/coreshop/zones/get',
        list: '/admin/coreshop/zones/list'
    },

    getItemClass: function() {
        return coreshop.zone.item;
    }
});
