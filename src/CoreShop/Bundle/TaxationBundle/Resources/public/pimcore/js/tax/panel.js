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
