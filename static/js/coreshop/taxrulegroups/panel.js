/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.taxrulegroups.panel');

pimcore.plugin.coreshop.taxrulegroups.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_tax_rule_groups_panel',
    storeId : 'coreshop_taxrulegroups',
    iconCls : 'coreshop_icon_tax_rule_groups',
    type : 'taxrulegroups',

    url : {
        add : '/plugin/CoreShop/admin_TaxRuleGroup/add',
        delete : '/plugin/CoreShop/admin_TaxRuleGroup/delete',
        get : '/plugin/CoreShop/admin_TaxRuleGroup/get',
        list : '/plugin/CoreShop/admin_TaxRuleGroup/list'
    }
});
