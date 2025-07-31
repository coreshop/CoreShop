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

coreshop.carrier.item  = Class.create(coreshop.carrier.item, {
    getSettings: function ($super) {
        var panel = $super(),
            data = this.data;

        panel.down("form").add([
            {
                xtype: 'coreshop.store',
                name: 'stores',
                multiSelect: true,
                typeAhead: false,
                value: data.stores
            },
            {
                xtype: 'coreshop.taxRuleGroup',
                value: data.taxRule
            }
        ]);

        panel.down("form").setDisabled(false);
        panel.down("form").setDisabled(!this.isAllowed('edit'));

        return panel;
    }
});
