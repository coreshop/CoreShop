/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.product.unit.panel');
coreshop.product.unit.panel = Class.create(coreshop.resource.panel, {

    layoutId: 'coreshop_product_unit_panel',
    storeId: 'coreshop_product_units',
    iconCls: 'coreshop_icon_product_units',
    type: 'coreshop_product_units',

    routing : {
        add: 'coreshop_product_unit_add',
        delete: 'coreshop_product_unit_delete',
        get: 'coreshop_product_unit_get',
        list: 'coreshop_product_unit_list'
    },

    getItemClass: function () {
        return coreshop.product.unit.item;
    }
});
