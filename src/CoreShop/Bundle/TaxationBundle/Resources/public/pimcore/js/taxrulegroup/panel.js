/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
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

    url: {
        add: '/admin/coreshop/tax_rule_groups/add',
        delete: '/admin/coreshop/tax_rule_groups/delete',
        get: '/admin/coreshop/tax_rule_groups/get',
        list: '/admin/coreshop/tax_rule_groups/list'
    },

    getItemClass: function() {
        return coreshop.taxrulegroup.item;
    }
});
