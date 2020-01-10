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

pimcore.registerNS('coreshop.product.unit.panel');
coreshop.product.unit.panel = Class.create(coreshop.resource.panel, {

    layoutId: 'coreshop_product_unit_panel',
    storeId: 'coreshop_product_units',
    iconCls: 'coreshop_icon_product_units',
    type: 'coreshop_product_units',

    url : {
        add: '/admin/coreshop/product_units/add',
        delete: '/admin/coreshop/product_units/delete',
        get: '/admin/coreshop/product_units/get',
        list: '/admin/coreshop/product_units/list'
    },

    getItemClass: function () {
        return coreshop.product.unit.item;
    }
});
