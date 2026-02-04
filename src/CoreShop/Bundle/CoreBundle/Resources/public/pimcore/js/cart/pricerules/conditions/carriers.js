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

pimcore.registerNS('coreshop.cart.pricerules.conditions.carriers');
coreshop.cart.pricerules.conditions.carriers = Class.create(coreshop.rules.conditions.abstract, {
    type: 'carriers',

    getForm: function () {
        var me = this;

        var carriers = {
            fieldLabel: t('coreshop_carrier'),
            typeAhead: true,
            listWidth: 100,
            width: 500,
            store: {
                type: 'coreshop_carriers'
            },
            displayField: 'identifier',
            valueField: 'id',
            forceSelection: true,
            multiSelect: true,
            triggerAction: 'all',
            name: 'carriers',
            maxHeight: 400,
            delimiter: false,
            value: me.data.carriers
        };

        if (this.data && this.data.carriers) {
            carriers.value = this.data.carriers;
        }

        carriers = new Ext.ux.form.MultiSelect(carriers);

        this.form = new Ext.form.Panel({
            items: [
                carriers
            ]
        });

        return this.form;
    }
});
