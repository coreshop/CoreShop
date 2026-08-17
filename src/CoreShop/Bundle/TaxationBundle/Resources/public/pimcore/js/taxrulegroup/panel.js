/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
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
