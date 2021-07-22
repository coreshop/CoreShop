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

pimcore.registerNS('coreshop.taxrulegroup.panel');
coreshop.taxrulegroup.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_tax_rule_groups_panel',
    storeId: 'coreshop_taxrulegroups',
    iconCls: 'coreshop_icon_tax_rule_groups',
    type: 'coreshop_taxrulegroups',

    routing: {
        add: 'coreshop_tax_rule_group_add',
        delete: 'coreshop_tax_rule_group_delete',
        get: 'coreshop_tax_rule_group_get',
        list: 'coreshop_tax_rule_group_list'
    },

    getItemClass: function() {
        return coreshop.taxrulegroup.item;
    }
});
