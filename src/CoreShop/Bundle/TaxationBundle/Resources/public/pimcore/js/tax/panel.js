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

pimcore.registerNS('coreshop.tax.panel');
coreshop.tax.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_taxes_panel',
    storeId: 'coreshop_tax_rates',
    iconCls: 'coreshop_icon_taxes',
    type: 'coreshop_taxes',

    routing: {
        add: 'coreshop_tax_rate_add',
        delete: 'coreshop_tax_rate_delete',
        get: 'coreshop_tax_rate_get',
        list: 'coreshop_tax_rate_list'
    },

    getItemClass: function() {
        return coreshop.tax.item;
    }
});
